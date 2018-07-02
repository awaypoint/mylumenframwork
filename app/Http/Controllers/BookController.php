<?php

namespace App\Http\Controllers;

use App\Modules\Book\BookRepository;
use Illuminate\Http\Request;

class BookController extends Controller
{
    private $_bookRepository;

    public function __construct(
        BookRepository $bookRepository
    )
    {
        parent::__construct();
        $this->_bookRepository = $bookRepository;
    }

    /**
     * 获取预定列表
     * @param Request $request
     * @return array
     */
    public function getBookList(Request $request)
    {
        list($page, $pageSigze, $order) = getPageSuit($request);

        $result = $this->_bookRepository->getBookList($request->all(), $page, $pageSigze, $order);
        return responseTo($result);
    }

    /**
     * 获取预定详情
     * @param Request $request
     * @return array
     */
    public function getBookDetail(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_bookRepository->getBookDetail($request->get('id'));
        return responseTo($result);
    }

    /**
     * 标记预订单
     * @param Request $request
     * @return array
     */
    public function dealBook(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_bookRepository->dealBook($request->get('id'), $request->all());
        return responseTo($result);
    }
    /**
     * 删除预定单
     */
    public function delBook(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $result = $this->_bookRepository->delBook($request->get('id'));
        return responseTo($result);
    }
}
