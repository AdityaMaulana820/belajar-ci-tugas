<?php

namespace App\Controllers;

use App\Models\ProductModel; 
use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;
use App\Models\DiskonModel; // 

class Home extends BaseController
{
    protected $product;
    protected $transaction;
    protected $transaction_detail;
    protected $diskonModel; // 

    function __construct()
    {
        $this->product = new ProductModel();
        $this->transaction = new TransactionModel();
        $this->transaction_detail = new TransactionDetailModel();
        $this->diskonModel = new DiskonModel(); // 
    }
    

public function index()
{
    $diskonModel = new DiskonModel();
    $today = date('Y-m-d');
    $diskon = $diskonModel->where('tanggal', $today)->first();

    if ($diskon) {
        session()->set('diskon', $diskon['nominal']);
    } else {
        session()->remove('diskon');
    }

    // existing produk fetch
    $produkModel = new \App\Models\ProductModel();
    $data['produk'] = $produkModel->findAll();
    return view('v_home', $data);
}

    public function profile()
    {
        helper('number');
        $username = session()->get('username');
        $data['username'] = $username;

        $buy = $this->transaction->where('username', $username)->findAll();
        $data['buy'] = $buy;

        $product = [];

        if (!empty($buy)) {
            foreach ($buy as $item) {
                $detail = $this->transaction_detail
                    ->select('transaction_detail.*, product.nama, product.harga, product.foto')
                    ->join('product', 'transaction_detail.product_id=product.id')
                    ->where('transaction_id', $item['id'])
                    ->findAll();

                if (!empty($detail)) {
                    $product[$item['id']] = $detail;
                }
            }
        }

        $data['product'] = $product;
{
        return view('v_profile', $data);
    }

    }
}
