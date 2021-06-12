<?php

namespace Core\Foundation;

interface Repository
{
	public function getModel();

    public function setModel($model);

    public function getAll();

    public function getAllPaginated($count);

    public function getById($id);

    public function requireById($id);

    public function getNew($attributes = []);

    public function save($data);

    public function delete($model);
}