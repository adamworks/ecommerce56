<?php

namespace Core\Support\Uploader;

use Storage;

use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use Orchestra\Imagine\Facade as Imagine;
use Imagine\Filter\Basic\WebOptimization;
use Core\Support\Uploader\ImageResize;

class UploaderService
{
	protected $ftpStorageConfigName 	= 'public_media';
	protected $localStorageConfigName 	= 'local_media';

	public function __construct()
	{
		//
	}

	/**
     * Full action of uploading file, including croping image, optimization image and also uploading file to our image server.
     *
     * @param  string  	$upload_path 
     * @param  file  	$file
     * @param  array  	$config 
     * @param  boolean  $is_crop 
     * @return array
     */
	public function upload($upload_path, $file, $config = array(), $is_crop = false)
	{
		$config = empty($config) ? \Config::get('image_resolution.resize-image.size') : $config;
		// validate image first, only png and jpeg only
		if ( $this->validateImage($file) ) {

			// define file name
			$file_name = str_random(3) . '-' . date('dmYhis') . '.' . $file->getClientOriginalExtension();
			
			// upload original file
			$this->uploadOriginalFile($upload_path, $file, $config, $is_crop, $file_name);
			
			// crop image
			$this->crop($upload_path, $file, $config, $is_crop, $file_name);
			
			// after upload process success, we delete local server directory
			Storage::disk($this->localStorageConfigName)->deleteDirectory($upload_path);

			return array('status' => 'success', 'message' => 'File berhasil diupload', 'file_name' => $file_name);
		} else {
			return array('status' => 'error', 'message' => 'File tidak didukung!');
		}
	}

	/**
     * Full action of deleting file, including deleting croped image.
     *
     * @param  string  	$delete_path 
     * @param  array  	$config 
     * @return array
     */
	public function delete($delete_path, $file_name, $config = array())
	{
		$config = empty($config) ? \Config::get('image_resolution.resize-image.size') : $config;

		// deleting original file image
		$original_file_name = 'original-'.$file_name;
		Storage::disk($this->ftpStorageConfigName)->delete($delete_path . $original_file_name);
		
		// deleting croped file image
		foreach ($config as $value) {
			$resized_file_name = $value['width'].'x'.$value['height'].'-'. $file_name;
			$delete_path_croped_image = $delete_path . $resized_file_name;
			Storage::disk($this->ftpStorageConfigName)->delete($delete_path_croped_image);
		}

		return array('status' => 'success', 'message' => 'File berhasil dihapus');
	}

	/**
     * To validating an upload image.
     *
     * @param  file  	$file
     * @return boolean
     */
	public function validateImage($file)
	{
		$detectedType = exif_imagetype($file);
		$allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG);
		return in_array($detectedType, $allowedTypes);
	}

	/**
     * Uploading original file, optimization original image and also uploading original file to our image server.
     *
     * @param  string  	$upload_path 
     * @param  file  	$file
     * @param  array  	$config 
     * @param  boolean  $is_crop 
     * @param  string   $file_name 
     * @return void
     */
	public function uploadOriginalFile($upload_path, $file, $config = array(), $is_crop = false, $file_name)
	{
		$config = empty($config) ? \Config::get('image_resolution.resize-image.size') : $config;
		$local_root_path		= \Config::get('filesystems.disks.'.$this->localStorageConfigName.'.root');
		$local_root_path 		= substr($upload_path, 0, 1) != '/' ? $local_root_path.'/' : $local_root_path;
		$original_image_path 	= $upload_path . 'original-' . $file_name;
		
		// uploading image to local server and optimize that image
		$file_contents = \File::get($file);
		Storage::disk($this->localStorageConfigName)->put($original_image_path, $file_contents);
		$this->optimizeImage($local_root_path.$original_image_path);
		
		// after image optimized, we upload that file to image server
		$file_original = Storage::disk($this->localStorageConfigName)->get($original_image_path);
		Storage::disk($this->ftpStorageConfigName)->put($original_image_path, $file_original, 'public');
	}

	/**
     * Croping original file, optimization croped image and also uploading croped file to our image server.
     *
     * @param  string  	$upload_path 
     * @param  file  	$file
     * @param  array  	$config 
     * @param  boolean  $is_crop 
     * @param  string   $file_name 
     * @return void
     */
	public function crop($upload_path, $file, $config = array(), $is_crop = false, $file_name)
	{
		$config = empty($config) ? \Config::get('image_resolution.resize-image.size') : $config;

		foreach ($config as $key => $value) {
			// define original image path and resized image path to upload
			$size_prefix 			= $value['width'] . 'x' . $value['height'] . '-';
			$local_root_path		= \Config::get('filesystems.disks.'.$this->localStorageConfigName.'.root');
			$local_root_path 		= substr($upload_path, 0, 1) != '/' ? $local_root_path.'/' : $local_root_path;
			$original_image_path 	= $upload_path . 'original-' . $file_name;
			$resized_image_path 	= $upload_path . $size_prefix . $file_name;

			// croping file
			$image = new ImageResize( $local_root_path.$original_image_path );
			if ($is_crop == true) {
				$image->crop($value['width'], $value['height']);
			} else {
				$image->resizeToWidth($value['width']);
			}
			$image->save( $local_root_path.$resized_image_path );
			
			// optimize croped image
			$this->optimizeImage( $local_root_path.$resized_image_path );

			// uploading croped image from local server to image server
			$file_resized_contents = Storage::disk($this->localStorageConfigName)->get($resized_image_path);
			Storage::disk($this->ftpStorageConfigName)->put($resized_image_path, $file_resized_contents, 'public');

			// after upload croped image success, we delete image at local server
			Storage::disk($this->localStorageConfigName)->delete($resized_image_path);
		}
	}

	/**
     * To optimizing Image file size.
     *
     * @param  string  	$file_path 
     * @return WebOptimization
     */
	public function optimizeImage($file_path)
	{
		$optimization 	 = new WebOptimization($file_path);
		$optimization->apply(Imagine::open($file_path));
		return $optimization;
	}
}