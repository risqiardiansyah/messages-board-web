<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\MainRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class MainController extends ApiController
{
  private $mainRepo;
  public function __construct(MainRepository $mainRepo)
  {
    $this->mainRepo = $mainRepo;
  }

  public function validateThis($request, $rules = array())
  {
    return Validator::make($request->all(), $rules);
  }

  public function getAllPesan(Request $request)
  {
    $result = $this->mainRepo->getAllPesan($request);

    if ($result['success']) {
      return $this->sendResponse(200, 'Success', $result['data']);
    } else {
      return $this->sendError(400, 'Error', $result['msg']);
    }
  }

  public function createPesan(Request $request)
  {
    $rules = [
      'nama' => 'required',
      'email' => 'required|email',
      'isi' => 'required',
    ];

    $validator = $this->validateThis($request, $rules);

    if ($validator->fails()) {
      return $this->sendError(1, 'Params not complete', validationMessage($validator->errors()));
    }

    if (
      !str_ends_with($request->email, '.id') &&
      !str_ends_with($request->email,  '.net') &&
      !str_ends_with($request->email, '.com')
    ) {
      return $this->sendError(1, 'Params not complete', (object)[
        'email' => 'Email must end in .id, .net, atau .com'
      ]);
    }

    $result = $this->mainRepo->createPesan($request);

    if ($result['success']) {
      return $this->sendResponse(200, 'Success', []);
    } else {
      return $this->sendError(400, 'Error', $result['msg']);
    }
  }

  public function updatePesan(Request $request)
  {
    $rules = [
      'id' => 'required',
      'nama' => 'required',
      'email' => 'required|email',
      'isi' => 'required',
    ];

    $validator = $this->validateThis($request, $rules);

    if ($validator->fails()) {
      return $this->sendError(1, 'Params not complete', validationMessage($validator->errors()));
    }

    if (
      !str_ends_with($request->email, '.id') &&
      !str_ends_with($request->email,  '.net') &&
      !str_ends_with($request->email, '.com')
    ) {
      return $this->sendError(1, 'Params not complete', (object)[
        'email' => 'Email must end in .id, .net, atau .com'
      ]);
    }

    $result = $this->mainRepo->updatePesan($request);

    if ($result['success']) {
      return $this->sendResponse(200, 'Success', $result['data']);
    } else {
      return $this->sendError(400, 'Error', $result['msg']);
    }
  }

  public function deletePesan($id)
  {
    $result = $this->mainRepo->deletePesan($id);

    if ($result['success']) {
      return $this->sendResponse(200, 'Success', []);
    } else {
      return $this->sendError(400, 'Error', $result['msg']);
    }
  }
}
