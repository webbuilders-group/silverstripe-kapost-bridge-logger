<?php
/**
 * Class KapostBridgeLog
 *
 * @property string $Method
 * @property string $Request
 * @property string $Response
 * @property string $UserAgent
 * @mixin LenovoKapostBridgeLog
 */
class KapostBridgeLog extends DataObject {
    private static $db=array(
                            'Method'=>'Varchar(100)',
                            'Request'=>'Text',
                            'Response'=>'Text',
                            'UserAgent'=>'Text'
                         );
    
    private static $default_sort='Created DESC, ID DESC';
    
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
    
    private $reference_object=false;
    
    
    /**
     * Gets the title of this log which is a combination of the method called and the date/time
     * @return {string} Title of the log entry
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
        $request=$this->Request;
        if(!empty($request)) {
            $doc=new DomDocument('1.0');
            $doc->preserveWhiteSpace=false;
            $doc->formatOutput=true;
            if(!@$doc->loadXML($request)) {
                return _t('KapostBridgeLogViewer.REQUEST_PARSE_ERROR', '_Could not Parse Request');
            }
            
            return $doc->saveXML();
        }
    }
    
    /**
     * Gets the response formatted with line breaks
     * @return {string} Raw XML Response from SilverStripe
     */
    public function getResponseFormatted() {
        $response=$this->Response;
        if(!empty($response)) {
            $doc=new DomDocument('1.0');
            $doc->preserveWhiteSpace=false;
            $doc->formatOutput=true;
            if(!@$doc->loadXML($response)) {
                return _t('KapostBridgeLogViewer.RESPONSE_PARSE_ERROR', '_Could not Parse Response');
            }
            
            return $doc->saveXML();
        }
    }
    
    /**
     * Gets the referenced object of this kapost bridge log entry
     */ 
    public function getReferenceObject() {
        if($this->reference_object===false) {
            $kapostRefID=false;
            
            if($this->Method=='metaWeblog.getPost' || $this->Method=='metaWeblog.editPost') {
                $xml=simplexml_load_string($this->Request);
                if($xml) {
                    $kapostRefID=$xml->params->param[0]->value->string->__toString();
                }
            }else if($this->Method=='metaWeblog.newPost' || $this->Method=='kapost.getPreview' || $this->Method=='metaWeblog.newMediaObject') {
                $xml=simplexml_load_string($this->Response);
                if($xml && !isset($xml->fault)) {
                    if($this->Method=='metaWeblog.newPost') {
                        $kapostRefID=$xml->params->param[0]->value->string->__toString();
                    }else if($this->Method=='kapost.getPreview') {
                        $kapostRefID=$xml->params->param[0]->value->struct->member[1]->value->string->__toString();
                    }else if($this->Method=='metaWeblog.newMediaObject') {
                        //Attempt to lookup the referenced file
                        $fileURL=$xml->params->param[0]->value->struct->member[1]->value->string->__toString();
                        if(!empty($fileURL)) {
                            $file=KapostService::find_file_by_url($fileURL);
                            if(!empty($file) && $file!==false && $file->ID>0) {
                                $this->reference_object=$file;
                                return $this->reference_object;
                            }else {
                                $this->reference_object=null;
                                return;
                            }
                        }else {
                            $this->reference_object=null;
                            return;
                        }
                    }
                }
            }else {
                $this->reference_object=null;
                return;
            }
            
            
            //If we have a 
            if(!empty($kapostRefID)) {
                Versioned::reset(); //Reset versioned to remove filters
                
                //Try looking for a page
                $page=SiteTree::get()->filter('KapostRefID', Convert::raw2sql($kapostRefID))->first();
                if(!empty($page) && $page!==false && $page->exists()) {
                    $this->reference_object=$page;
                }else {
                    //Allow extensions to add their own logic
                    $extensionResult=array_filter($this->extend('updateObjectLookup', $kapostRefID), function($item) {return is_object($item);});
                    $extensionResult=array_shift($extensionResult);
                    if(!empty($extensionResult) && $extensionResult!==false && $extensionResult->exists()) {
                        $this->reference_object=$extensionResult;
                    }else {
                        //Look for a kapost object that is not a preview
                        $kapostObj=KapostObject::get()->filter('KapostRefID', Convert::raw2sql($kapostRefID))->first();
                        if(!empty($kapostObj) && $kapostObj!==false && $kapostObj->exists() && $kapostObj->IsKapostPreview==false) {
                            $this->reference_object=$kapostObj;
                        }else {
                            //No result so set to null
                            $this->reference_object=null;
                        }
                    }
                }
            }else {
                //No Kapost Reference ID so set to null
                $this->reference_object=null;
            }
        }
        
        return $this->reference_object;
    }
}
?>