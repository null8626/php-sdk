<?php

/**
 * This file is respectively apart of the TopPHP project.
 *
 * Copyright (c) 2021-present James Walston
 * Some rights are reserved.
 *
 * This copyright is subject to the MIT license which
 * fully entails the details in the LICENSE file.
 */

namespace DBL;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

abstract class Webhook extends Controller {
  public function main(Request $request) {
    if ($request->header("Authorization") !== $this->getAuthorization()) {
      return response("Unauthorized", 401);
    }

    $data = $request->json()->all();

    if (!empty($data)) {
      $this->callback($data);
      
      return response("", 204);
    }

    return response("Bad request", 400);
  }

  /**
   * Retrieves the authorization token used to authorize incoming webhook requests.
   *
   * @return  string
   */
  public abstract function getAuthorization();

  /**
   * Receives the JSON body sent from Top.gg containing webhook event information.
   *
   * @param   array    $data   The JSON body sent from Top.gg containing webhook event information.
   * @return  void
   */
  public abstract function callback(array $data);
}

?>