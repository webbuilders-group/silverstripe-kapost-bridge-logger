<?php
class KapostBridgeLog extends DataObject {
    private static $db=array(
                            'Method'=>'Varchar(100)',
                            'Request'=>'Text',
                            'Response'=>'Text'
                         );
    
    private static $default_sort='Created DESC';
    
    /**
     * Time in days to expire the log entries
     * @config KapostBridgeLog.log_expire_days
     * @var int
     * @default 30
     */
    private static $log_expire_days=30;
    
    private static $casting=array(
                                'RequestFormatted'=>'Text',
                                'ResponseFormatted'=>'Text'
                            );
    
    /**
     * @TODO
     */
    public function getTitle() {
        return $this->Method.': '.$this->dbObject('Created')->FormatFromSettings();
    }
    
    /**
     * Cleans up logs older than X days after writing
     */
    protected function onAfterWrite() {
        parent::onAfterWrite();
        
        //Clean up old logs
        $oldLogs=KapostBridgeLog::get()->filter('Created:LessThan', date('Y-m-d H:i:s', strtotime('-'.self::config()->log_expire_days.' days')));
        if($oldLogs->count()>0) {
            foreach($oldLogs as $log) {
                $log->delete();
            }
        }
    }
    
    /**
     * Gets the request formatted with line breaks
     * @return {string} Raw XML Request from Kapost
     */
    public function getRequestFormatted() {
        $doc=new DomDocument('1.0');
        $doc->preserveWhiteSpace=false;
        $doc->formatOutput=true;
        $doc->loadXML($this->Request);
        
        return $doc->saveXML();
    }
    
    /**
     * Gets the response formatted with line breaks
     * @return {string} Raw XML Response from SilverStripe
     */
    public function getResponseFormatted() {
        $doc=new DomDocument('1.0');
        $doc->preserveWhiteSpace=false;
        $doc->formatOutput=true;
        $doc->loadXML($this->Response);
        
        return $doc->saveXML();
    }
}
?>