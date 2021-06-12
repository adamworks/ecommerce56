<?php
if (! function_exists('getPlaceHoldItUrl')) {
	/**
     * Return a placehold it url.
     *
     * @param  int     	$width
     * @param  int   	$height
     * @return string
     */
	function getPlaceHoldItUrl($width = 50, $height = 50) 
	{
        //return sprintf("https://via.placeholder.com/%sx%s", $width, $height);
        return sprintf("https://via.placeholder.com/%sx%s?text=No+Images",$width,$height);
	}
}

if (! function_exists('successResponse') && function_exists('response')) {
    /**
     * Return a new response from the application.
     *
     * @param  string     $codeMessage
     * @param  mix        $data
     */
    function successResponse(string $codeMessage = '', $data)
    {
        $content = [
            "code" => 200,
            "code_message" => $codeMessage,
            "code_type" => "success",
            "data" => $data
        ];

        return response($content, $status = 200);
    }
}

if (! function_exists('errorResponse') && function_exists('response')) {
    /**
     * Return a new response from the application.
     *
     * @param  string     $codeMessage
     * @param  mix        $data
     */
    function errorResponse(string $codeMessage = '', $data)
    {
        $content = [
            "code" => 200,
            "code_message" => $codeMessage,
            "code_type" => "error",
            "data" => []
        ];

        return response($content, $status = 200);
    }
}

if (! function_exists('formValidatorExeptionResponse') && function_exists('response')) {
    /**
     * Return a new response from the application.
     *
     * @param  string     $codeMessage
     * @param  mix        $data
     */
    function formValidatorExeptionResponse(string $codeMessage = '', $data)
    {
        if (empty($codeMessage)) {
            $codeMessage = trans('validation.invalid_input');
        }
        
        $content = [
            'code' => 403,
            'code_message' => $codeMessage,
            'code_type' => 'validationFail',
            'data' => $data,
        ];

        return response($content, $status = 403);
    }
}

if (! function_exists('imagePath')) {
    /**
     * Return a image path from the application.
     *
     * @param  string     $files
     */
    function imagePath(string $files = '')
    {
        return public_path('media/'.$files);
    }
}

if (! function_exists('imageUrl')) {
    /**
     * Return a image path from the application.
     *
     * @param  string     $files
     */
    function imageUrl(string $files = '')
    {
        if (env('APP_ENV') == 'production') {
            return env('S3_URL').'/media/'.$files;
        }

        return url('media/'.$files);
    }
}