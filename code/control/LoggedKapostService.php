<?php
class LoggedKapostService extends KapostService {
    /**
     * Whether or not to ignore 404 errors
     * @config LoggedKapostService.ignore_not_found
     * @default true
     */
    private static $ignore_not_found=true;
    
    
    /**
     * Intercepts the result of Controller::handleRequest() to log the values into the database
     * @param {SS_HTTPRequest} $request The SS_HTTPRequest object that is responsible for distributing request parsing.
	 * @return {SS_HTTPResponse} The response that this controller produces, including HTTP headers such as redirection info
     */
    public function handleRequest(SS_HTTPRequest $request, DataModel $model) {
        $response=parent::handleRequest($request, $model);
        
        
        //Log Request
        try {
            $xml=@simplexml_load_string($this->request->getBody());
            if($xml) {
                //Strip sensitive info from request
                if(strpos($xml->methodName, 'metaWeblog.')===0 || strpos($xml->methodName, 'kapost.')===0) {
                    $xml->params->param[2]->value->string='['._t('LoggedKapostService.PASSWORD_FILTERED', '_PASSWORD FILTERED').']';
                    
                    //For metaWeblog.newMediaObject requests clear the bits for the file before writing
                    if($xml->methodName=='metaWeblog.newMediaObject') {
                        $xml->params->param[3]->value->struct->member[2]->value->base64='['._t('LoggedKapostService.BITS_FILTERED', '_BASE64 BITS FILTERED').']';
                    }
                }
                
                
                //Write a log entry
                $methodName=$xml->methodName->__toString();
                $requestXML=$xml->asXML();
            }else {
                $methodName=_t('LoggedKapostService.UNKNOWN_METHOD', '_Unknown Method');
                $requestXML=_t('LoggedKapostService.REQUEST_PARSE_ERROR', '_Request Parsing Error');
            }
            
            
            //If the ignore not found configuration option is set to true and the response is a 404 do not log
            if($this->config()->ignore_not_found && $response instanceof SS_HTTPResponse && $response->getStatusCode()==404) {
                return $response;
            }
            
            
            //Write a log entry
            $logEntry=new KapostBridgeLog();
            $logEntry->Method=$methodName;
            $logEntry->Request=$requestXML;
            $logEntry->Response=($response instanceof SS_HTTPResponse ? $this->cleanDebugInfo($response->getBody()):(is_string($response) ? $this->cleanDebugInfo($response):null));
            $logEntry->UserAgent=$request->getHeader('User-Agent');
            $logEntry->write();
        }catch(Exception $e) {}
        
        
        return $response;
    }
    
    /**
     * Strips the server debug information from the log
     * @param {string} $xml XML String to be parsed
     * @return {string} XML String with the debug info stripped out
     */
    protected function cleanDebugInfo($xml) {
        return preg_replace('/<\!-- SERVER DEBUG INFO \(BASE64 ENCODED\)\:(\s+)([A-Za-z0-9\/=+]*)(\s+)-->/s', '', $xml);
    }
}
?>