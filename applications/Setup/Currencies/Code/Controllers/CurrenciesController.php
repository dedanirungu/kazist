<?php

/*
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */

/**
 * Description of CurrenciesController
 *
 * @author sbc
 */

namespace Setup\Currencies\Code\Controllers;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Controller\BaseController;
use Setup\Currencies\Code\Models\CurrenciesModel;

class CurrenciesController extends BaseController {

    public function cronfetchcurrencyAction() {
        
        $currencyModel = new CurrenciesModel();
        $currencyModel->fetchCurrency();

        return $this->json($data);
    }

}
