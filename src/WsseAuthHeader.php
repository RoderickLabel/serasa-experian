<?php

namespace SerasaExperian;

final class WsseAuthHeader extends \SoapHeader {
    
    private $wss_ns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
    private $wsu_ns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';

    function __construct($user = '', $pass = '') {
        $data = $this->getData($user, $pass);
        
        parent::__construct($this->wss_ns, 'Security', $data, true);
    }
    
    private function getData($user = '', $pass = '') {
        
        $auth = $this->getAuth($user, $pass);
        
        $username_token = $this->getUsernameToken($auth);
        
        return new \SoapVar(
            new \SoapVar($username_token, SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'UsernameToken', $this->wss_ns), 
                SOAP_ENC_OBJECT, 
                NULL, 
                $this->wss_ns, 
                'Security', 
                $this->wss_ns);
    }
    
    private function getAuth($user = '', $pass = '') {
        $created = gmdate('Y-m-d\TH:i:s\Z');
        $nonce = mt_rand();
        $passdigest = base64_encode(pack('H*', sha1(pack('H*', $nonce) . pack('a*', $created) . pack('a*', $pass))));
        
        $auth = new \stdClass();
        $auth->Username = new \SoapVar($user, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
        $auth->Password = new \SoapVar($pass, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
        $auth->Nonce = new \SoapVar($passdigest, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
        $auth->Created = new \SoapVar($created, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wsu_ns);
        
        return $auth;
    }
    
    private function getUsernameToken($auth) {
        $username_token = new \stdClass();
        $username_token->UsernameToken = new \SoapVar($auth, SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'UsernameToken', $this->wss_ns);
        return $username_token;
    }
    
    
    
    
}