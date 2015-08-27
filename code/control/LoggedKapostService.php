<?php
class LoggedKapostService extends KapostService {
    /**
     * Intercepts the result of Controller::handleRequest() to log the values into the database
     * @param {SS_HTTPRequest} $request The SS_HTTPRequest object that is responsible for distributing request parsing.
	 * @return {SS_HTTPResponse} The response that this controller produces, including HTTP headers such as redirection info
     */
    public function handleRequest(SS_HTTPRequest $request, DataModel $model) {
        $response=parent::handleRequest($request, $model);
        
        
        //Log Request
        if(!Director::isTest()) {
            try {
                $xml=simplexml_load_string($this->request->getBody());
                if($xml) {
                    //Strip sensitive info from request
                    if($xml->methodName!='system.listMethods') {
                        $xml->params->param[2]->value->string='[PASSWORD FILTERED]';
                        
                        //For metaWeblog.newMediaObject requests clear the bits for the file before writing
                        if($xml->methodName=='metaWeblog.newMediaObject') {
                            $xml->params->param[3]->value->struct->member[2]->value->base64='[BASE64 BITS FILTERED]';
                        }
                    }
                    
                    
                    //Write a log entry
                    $logEntry=new KapostBridgeLog();
                    $logEntry->Method=$xml->methodName->__toString();
                    $logEntry->Request=$xml->asXML();
                    $logEntry->Response=($response instanceof SS_HTTPResponse ? $response->getBody():(is_string($response) ? $response:null));
                    $logEntry->write();
                }
            }catch(Exception $e) {}
        }
        
        
        return $response;
    }
}
?>