<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(protected Payment $model) {}

    public function index(){

        $payments = $this->model->get();

        foreach($payments as $payment){
            $payment->is_active = $payment->is_active ? 1 : 0;
        }

        return sendResponse($payments,200);
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required'
        ]);

        return sendResponse($this->model->create($request->all()),201,'Payment created successfully!');
    }

    public function update(Request $request){
        $request->validate([
            'id' => 'required',
            'name' => 'required'
        ]);

        $payment = $this->model->find($request->id);
        if(!$payment){
            return sendResponse(null,404,'Payment not found');
        }

        $payment->name = $request->name;
        $payment->save();

        return sendResponse($payment,200,'Payment updated successfully!');

    }

    public function destroy(Request $request){
        $request->validate([
            'id' => 'required'
        ]);

        $payment = $this->model->find($request->id);
        if(!$payment){
            return sendResponse(null,404,'Payment not found');
        }

        $payment->delete();

        return sendResponse(null,204,'Payment deleted successfully!');
    }

}
