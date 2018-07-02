<?php

namespace App\Modules\Book\Facades;

use App\Modules\Book\BookRepository;

class BookFacades
{
    private $_bookRepository;

    public function __construct(BookRepository $repository)
    {
        $this->_bookRepository = $repository;
    }
}
