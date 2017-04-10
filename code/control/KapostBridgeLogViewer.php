<?php
/**
 * Class KapostBridgeLogViewer
 *
 */
class KapostBridgeLogViewer extends LeftAndMain implements PermissionProvider {
    private static $url_segment='kapost-bridge-logs';
    private static $tree_class='KapostBridgeLog';
    private static $log_page_length=20;
    
    private static $allowed_actions=array(
                                        'view'
                                    );
    
    private $_menu_updated=false;
    
    
    /**
     * Adds requirements needed for the log viewer
     */
    public function init() {
        parent::init();
        
        Requirements::css('kapost-bridge-logger/css/KapostBridgeLogViewer.css');
        Requirements::javascript('kapost-bridge-logger/javascript/KapostBridgeLogViewer.js');
    }
    
    /**
     * Gets the form used for viewing a time log
     */
    public function getEditForm($id=null, $fields=null) {
        $record=$this->currentPage();
        if($this->action=='view' && $record) {
            $fields=new FieldList(
                                new HeaderField('LogHeader', _t('KapostBridgeLogViewer.VIEWING_ENTRY', '_Viewing Log Entry: {datetime}', array('datetime'=>$record->dbObject('Created')->FormatFromSettings())), 3),
                                new ReadonlyField('UserAgent', _t('KapostBridgeLogViewer.USER_AGENT', '_Requestor User Agent')),
                                new ReadonlyField('Method', _t('KapostBridgeLogViewer.METHOD', '_Method')),
                                ToggleCompositeField::create('RequestData', _t('KapostBridgeLogViewer.KAPOST_REQUEST', '_Kapost Request'), new FieldList(
                                                                                                ReadonlyField::create('RequestFormatted', '')
                                                                                                    ->setTemplate('KapostBridgeLogField')
                                                                                                    ->addExtraClass('log-contents cms-panel-layout')
                                                                                            ))->setHeadingLevel(3),
                                ToggleCompositeField::create('ResponseData', _t('KapostBridgeLogViewer.SILVERSTRIPE_RESPONSE', '_SilverStripe Response'), new FieldList(
                                                                                                ReadonlyField::create('ResponseFormatted', '')
                                                                                                    ->setTemplate('KapostBridgeLogField')
                                                                                                    ->addExtraClass('log-contents cms-panel-layout')
                                                                                            ))->setHeadingLevel(3)
                            );
            
            
            $refObj=$record->ReferenceObject;
            if(!empty($refObj) && $refObj!==false && $refObj->exists()) {
                if(method_exists($refObj, 'CMSEditLink')) {
                    $fields->insertBefore(new KapostLogLinkField('CMSEditLink', _t('KapostBridgeLogViewer.REFERENCED_OBJECT', '_Referenced Object'), $refObj->CMSEditLink(), _t('KapostBridgeLogViewer.VIEW_REFERENCED_OBJECT', '_View Referenced Object')), 'RequestData');
                }else if($refObj instanceof File) {
                    $refObjLink=Controller::join_links(LeftAndMain::config()->url_base, AssetAdmin::config()->url_segment, 'EditForm/field/File/item', $refObj->ID, 'edit');
                    $fields->insertBefore(new KapostLogLinkField('CMSEditLink', _t('KapostBridgeLogViewer.REFERENCED_OBJECT', '_Referenced Object'), $refObjLink, _t('KapostBridgeLogViewer.VIEW_REFERENCED_OBJECT', '_View Referenced Object')), 'RequestData');
                }
            }
        }else {
            $fields=new FieldList();
        }
        
        
        $form=new CMSForm($this, 'EditForm', $fields, new FieldList());
        $form->setResponseNegotiator($this->getResponseNegotiator());
        $form->addExtraClass('cms-edit-form center');
        $form->setAttribute('data-layout-type', 'border');
        $form->setTemplate($this->getTemplatesWithSuffix('_EditForm'));
        $form->setAttribute('data-pjax-fragment', 'CurrentForm');
        $form->setHTMLID('Form_EditForm');
        
        if($record) {
            $form->loadDataFrom($record);
        }
        
        
        return $form;
    }
    
    /**
     * Handles requests to view logs
     * @return {mixed} Returns PjaxResponseNegotiator if we're using ajax, 404 if we're using ajax and the response cannot be found, redirect if not found in a non-ajax request, and an array if found in an ajax request.
     */
    public function view() {
        //If we're dealing with an ajax request return the form's html
        if(Director::is_ajax()) {
            //If the log cannot be found 404
            if(!$this->currentPage()) {
                return $this->httpError(404);
            }
            
            return $this->getResponseNegotiator()->respond($this->request);
        }
        
        
        //If the log cannot be found redirect to the main screen
        if(!$this->currentPage()) {
            return $this->redirect($this->Link());
        }
        
        
        //Other wise render normally
        return array();
    }
    
    public function MainMenu($cached=true) {
        $menu=parent::MainMenu($cached);
        
        if(!$cached || !$this->_menu_updated) {
            $item=$this->_cache_MainMenu->find('Code', 'KapostAdmin');
            if($item) {
                $item->LinkingMode='current';
            }
        }
        
        return $menu;
    }
    
    /**
     * Current menu item is the KapostAdmin
     * @return {ArrayData}
     */
    public function MenuCurrentItem() {
        return $this->MainMenu()->find('Code', 'KapostAdmin');
    }
    
    /**
     * Form used for displaying the currently logged items
     */
    public function LogsForm() {
        $fields=new FieldList(
                            DropdownField::create('CalledMethod', _t('KapostBridgeLogViewer.CALLED_METHOD', '_Called Method'), array(
                                                                                    'blogger.getUsersBlogs'=>'blogger.getUsersBlogs',
                                                                                    'kapost.getPreview'=>'kapost.getPreview',
                                                                                    'metaWeblog.editPost'=>'metaWeblog.editPost',
                                                                                    'metaWeblog.getCategories'=>'metaWeblog.getCategories',
                                                                                    'metaWeblog.getPost'=>'metaWeblog.getPost',
                                                                                    'metaWeblog.newMediaObject'=>'metaWeblog.newMediaObject',
                                                                                    'metaWeblog.newPost'=>'metaWeblog.newPost',
                                                                                    'system.listMethods'=>'system.listMethods'
                                                                                ))->setEmptyString('--- '._t('KapostBridgeLogViewer.FILTER_BY_METHOD', '_Filter by Method').' ---'),
                            $startDate=new DatetimeField('LogStartDate', _t('KapostBridgeLogViewer.START_DATE_TIME', '_Start Date/Time')),
                            $endDate=new DatetimeField('LogEndDate', _t('KapostBridgeLogViewer.END_DATE_TIME', '_End Date/Time'))
                        );
        
        
        $startDate->getDateField()->setConfig('showcalendar', true)
                                    ->setAttribute('placeholder', _t('KapostBridgeLogViewer.DATE_FORMAT', '_MMM d, y'))
                                    ->setDescription(_t('KapostBridgeLogViewer.DATE_FORMAT_DESC', '_e.g. Jan 27, 2016'));
        
        $startDate->getTimeField()
                                ->setAttribute('placeholder', _t('KapostBridgeLogViewer.TIME_FORMAT', '_h:mm:ss a'))
                                ->setDescription(_t('KapostBridgeLogViewer.TIME_FORMAT_DESC', '_e.g. 12:15:13 PM'));
        
        
        $endDate->getDateField()->setConfig('showcalendar', true)
                                ->setAttribute('placeholder', _t('KapostBridgeLogViewer.DATE_FORMAT', '_MMM d, y'))
                                ->setDescription(_t('KapostBridgeLogViewer.DATE_FORMAT_DESC', '_e.g. Jan 27, 2016'));
        
        $endDate->getTimeField()
                                ->setAttribute('placeholder', _t('KapostBridgeLogViewer.TIME_FORMAT', '_h:mm:ss a'))
                                ->setDescription(_t('KapostBridgeLogViewer.TIME_FORMAT_DESC', '_e.g. 12:15:13 PM'));
        
        
        $actions=new FieldList(
                                FormAction::create('doApplyFilters', _t('KapostBridgeLogViewer.APPLY_FILTER', '_Apply Filter'))->addExtraClass('ss-ui-action-constructive')->setUseButtonTag(true),
                                Object::create('ResetFormAction', 'clear', _t('KapostBridgeLogViewer.RESET', '_Reset'))->setUseButtonTag(true)
                            );
        
        
        $form=new Form($this, 'LogsForm', $fields, $actions);
        $form->addExtraClass('log-search-form')
            ->setFormMethod('GET')
            ->setFormAction($this->Link())
            ->disableSecurityToken()
            ->unsetValidator();
        
        
        // Load the form with previously sent search data
        $getVars=$this->request->getVars();
        
        //Workaround for start date field with no date or time
        if(array_key_exists('LogStartDate', $getVars)) {
            if(array_key_exists('date', $getVars['LogStartDate']) && !array_key_exists('time', $getVars['LogStartDate'])) {
                $getVars['LogStartDate']['time']='00:00:00';
            }else if(!array_key_exists('date', $getVars['LogStartDate']) && array_key_exists('time', $getVars['LogStartDate'])) {
                unset($getVars['LogStartDate']); //Remove if there is no date present
            }
        }
        
        //Workaround for end date field with no date or time
        if(array_key_exists('LogEndDate', $getVars)) {
            if(array_key_exists('date', $getVars['LogEndDate']) && !array_key_exists('time', $getVars['LogEndDate'])) {
                $getVars['LogEndDate']['time']='23:59:59';
            }else if(!array_key_exists('date', $getVars['LogEndDate']) && array_key_exists('time', $getVars['LogEndDate'])) {
                unset($getVars['LogEndDate']); //Remove if there is no date present
            }
        }
        
        $form->loadDataFrom($getVars);
        
        
        return $form;
    }

	/**
	 * Renders a panel containing the logs available
	 * @return {string} HTML to be used in the template
	 */
	public function LogsPanel() {
		return $this->renderWith('KapostBridgeLogViewer_Logs');
	}
    
	/**
	 * Gets the logs currently in the database
	 * @return {DataList} Data List pointing to the logs in the database
	 */
    public function getLogs() {
        $logs=KapostBridgeLog::get();
        
        $filterFields=$this->LogsForm()->Fields();
        
        //Apply Called Method filter
        $var=$filterFields->dataFieldByName('CalledMethod')->Value();
        if(!empty($var)) {
            $logs=$logs->filter('Method', Convert::raw2sql($var));
        }
        
        //Apply Start Date Filter
        $dateTimeField=$filterFields->dataFieldByName('LogStartDate');
        $var=trim($dateTimeField->getDateField()->dataValue().' '.$dateTimeField->getTimeField()->dataValue());
        if(!empty($var)) {
            $logs=$logs->filter('Created:GreaterThan', Convert::raw2sql($var));
        }
        
        //Apply End Date Filter
        $var=$filterFields->dataFieldByName('LogEndDate')->Value();
        if(!empty($var) && $var!=' 00:00:00') {
            $logs=$logs->filter('Created:LessThan', Convert::raw2sql($var));
        }
        
        return PaginatedList::create($logs, $this->request)->setPageLength(self::config()->log_page_length);
    }
    
    /**
     * Gets the pjax response negotiator for this controller
     * @return PjaxResponseNegotiator
     */
    public function getResponseNegotiator() {
        if(!$this->responseNegotiator) {
            parent::getResponseNegotiator();
            
            $controller=$this;
            $this->responseNegotiator->setCallback('LogEntries', function() use(&$controller) {
                                            						return $controller->renderWith('KapostBridgeLogViewer_LogsList');
                                            					});
        }
        
        return $this->responseNegotiator;
    }
    
    public function Breadcrumbs($unlinked=false) {
        $sng=singleton('KapostAdmin');
        $crumbs=new ArrayList(array(
                                    new ArrayData(array(
                                        				'Title'=>$sng->SectionTitle(),
                                        				'Link'=>($unlinked ? false:$sng->Link())
                                        			))
                                ));
        
        $crumbs->merge(parent::Breadcrumbs($unlinked));
        
        return $crumbs;
    }
    
    /**
     * Provides the CMS_ACCESS_KapostBridgeLogViewer permission
     * @return {array} Map describing the permission for this cms panel
     */
    public function providePermissions() {
        return array(
                    'CMS_ACCESS_KapostBridgeLogViewer'=>array(
                                                            'name'=>_t(
                                                                    'CMSMain.ACCESS',
                                                                    "Access to '{title}' section",
                                                                    "Item in permission selection identifying the admin section. Example: Access to 'Files & Images'",
                                                                    array('title'=>_t('KapostBridgeLogViewer.MENUTITLE', 'Kapost Bridge Logs'))
                                                                ),
                                                            'category'=>_t('Permission.CMS_ACCESS_CATEGORY', 'CMS Access')
                                                        )
                );
    }
}
?>