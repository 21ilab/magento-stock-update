<?php
/**
 * Created by Afroze.S.
 * Date: 15/2/18
 * Time: 11:38 AM
 */

namespace Twentyone\UpdateStock\ServiceEntity;


class SoapEntity
{
    /**
     * @var \SoapClient
     */
    private $soapClient;

    /**
     * @param string $url
     * @return void
     */
    public function setConnection($url){
        $this->soapClient = new \SoapClient($url);
        //var_dump($this->soapClient->__getTypes());die;
    }

    /**
     * @param string $functionName
     * @param array $params
     * @return mixed
     */
    public function callFunction($functionName, $params) {
        if ($this->soapClient) {
            $res = $this->soapClient->__soapCall($functionName, $params);
            return $res;
        }
        return null;
    }

    /**
     * @param float $idAtelier
     * @param string $size
     * @return int|null
     */
    public function checkAvailabilityInAtelier($idAtelier, $size) {
        $availability = null;

        if ($this->soapClient) {
            $params = [
                'ID_ARTICOLO' => $idAtelier,
                'TAGLIA' => $this->getAtelierSize($size)
            ];
            $res = $this->callFunction('DisponibilitaVarianteTaglia', [$params]);
            $availability =  $res->DisponibilitaVarianteTagliaResult;
        }
        return $availability;
    }

    public function updateClient($email, $firstName, $lastName) {
        $res = null;
        if ($this->soapClient) {
            $params = [
                'EMAIL' => $email,
                'NOME' => $firstName,
                'COGNOME' => $lastName,
                'INDIRIZZO' => '',
                'CAP' => '',
                'CITTA' => '',
                'STATO' => '',
                'TEL' => '',
                'ESENTE' => '',
                'CELL' => '',
                'SESSO' => '',
                'FIDELITY' => '',
                'PI' => '.',
                'CODFIS' => '.'
            ];
            $res = $this->callFunction('AggiornaClienteCompletaFidelity', [$params]);
        }
        return $res;
    }

    public function updateOrder($email, $idAtelier, $size, $orderId, $address1, $address2, $address3, $price, $qty) {
        $string = "SALDI=SI|CAMBIO=1|ID_VALUTA=1||PREZZO_LISTINO=85|EMAIL_CLIENTE=".$email."|ID_CLINETE=".$email."|ID_ARTICOLO=".$idAtelier."|TAGLIA=".$this->getAtelierSize($size)."|CODICE=".$orderId."|DESTINAZIONE_RIGA1=".$address1."|DESTINAZIONE_RIGA2=".$address2."|DESTINAZIONE_RIGA3=".$address3."|PREZZO=".$price."|QTA=".$qty;
        $res = null;
        if ($this->soapClient) {
            $params = [
                'ParametriImpegni' => $string
            ];
            $res = $this->callFunction("SetImpegnoEsteso", [$params]);
        }
        return $res;
    }

    private function getAtelierSize($size) {
        if (is_numeric(substr($size, 0, 1)) && !is_numeric(substr($size, strlen($size)-1, 1))) {
            $preNum = substr($size, 0, strlen($size)-2);
            $size = $preNum."½";
        }
        return $size;
    }

    public function communicateShippingFare() {

    }
}