<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Setup\Currencies\Code\Models;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Model\BaseModel;
use Kazist\KazistFactory;
use Kazist\Service\Database\Query;

/**
 * Description of QuestionModel
 *
 * @author sbc
 */
class CurrenciesModel extends BaseModel {

    public $limit = 10;

    public function fetchCurrency() {

        $factory = new KazistFactory();
        $db = $factory->getDatabase();
        $query = new Query();

        $this->checkCurrencyFetchField();
        $currencies = $this->getCurrencies();

        if (!empty($currencies)) {
            foreach ($currencies as $key => $currency) {
                $this->updateCurrency($currency);
            }
        } else {
            $query->update('#__setup_currencies');
            $query->set('is_fetched', '0');
            $query->execute();
        }
    }

    public function updateCurrency($currency) {

        $factory = new KazistFactory();

        if ($currency->code <> '') {

            $rate_amount = $this->convertCurrency(1, 'USD', $currency->code);

            $data_obj = new \stdClass();
            $data_obj->id = $currency->id;
            $data_obj->rate = $rate_amount;
            $data_obj->is_fetched = 1;
            $factory->saveRecord('#__setup_currencies', $data_obj);
        }
    }

    function convertCurrency($amount, $from, $to) {
        $url = "https://www.google.com/payments/converter?a=$amount&from=$from&to=$to";
        $data = file_get_contents($url);
        preg_match("/<span class=bld>(.*)<\/span>/", $data, $converted);
        $converted = preg_replace("/[^0-9.]/", "", $converted[1]);
        return round($converted, 3);
    }

    public function getCurrencies() {

        $query = new Query();
        $query->select('swc.*');
        $query->from('#__setup_currencies', 'swc');
        $query->where('is_fetched=0 OR is_fetched IS NULL');

        $query->setFirstResult(0);
        $query->setMaxResults($this->limit);

        $records = $query->loadObjectList();

        return $records;
    }

}
