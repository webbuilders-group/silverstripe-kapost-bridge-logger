<?php
class KapostBridgeLogViewer extends LeftAndMain {
    private static $menu_icon='kapost-bridge-logger/images/icons/cms-icon.png';
    private static $url_segment='kapost-bridge-logs';
    private static $menu_priority=-0.5;
    private static $tree_class='KapostBridgeLog';
    
    private static $allowed_actions=array(
                                        'view'
                                    );
    
    public function init() {
        parent::init();
        
        Requirements::css('kapost-bridge-logger/css/KapostBridgeLogViewer.css');
    }
    
    /**
     * @TODO
     */
    public function getEditForm($id=null, $fields=null) {
        $record=$this->currentPage();
        if($this->action=='view' && $record) {
            $fields=new FieldList(
                                new HeaderField('LogHeader', _t('KapostBridgeLogViewer.VIEWING_ENTRY', '_Viewing Log Entry: {datetime}', array('datetime'=>$record->dbObject('Created')->FormatFromSettings())), 3),
                                new ReadonlyField('Method', _t('KapostBridgeLogViewer.METHOD', '_Method')),
                                ToggleCompositeField::create('RequestData', _t('KapostBridgeLogViewer.KAPOST_REQUEST', '_Kapost Request'), new FieldList(
                                                                                                ReadonlyField::create('RequestFormatted', '')->setTemplate('KapostBridgeLogField')
                                                                                            ))->setHeadingLevel(3),
                                ToggleCompositeField::create('ResponseData', _t('KapostBridgeLogViewer.SILVERSTRIPE_RESPONSE', '_SilverStripe Response'), new FieldList(
                                                                                                ReadonlyField::create('ResponseFormatted', '')->setTemplate('KapostBridgeLogField')
                                                                                            ))->setHeadingLevel(3)
                            );
        }else {
            $fields=new FieldList();
        }
        
        
        $form=new CMSForm($this, 'EditForm', $fields, new FieldList());
        $form->setResponseNegotiator($this->getResponseNegotiator());
        $form->addExtraClass('cms-content center');
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
     * @TODO
     */
    public function view() {
        if(Director::is_ajax()) {
            return $this->getEditForm()->forTemplate();
        }
        
        return array();
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
                                                                                ))->setEmptyString('---_Filter by Method---'),
                            $startDate=new DatetimeField('LogStartDate', _t('KapostBridgeLogViewer.START_DATE_TIME', '_Start Date/Time')),
                            $endDate=new DatetimeField('LogEndDate', _t('KapostBridgeLogViewer.END_DATE_TIME', '_End Date/Time'))
                        );
        
        $startDate->setConfig('showcalendar', true);
        $endDate->setConfig('showcalendar', true);
        
        
        $actions=new FieldList(
                                FormAction::create('doApplyFilters', _t('KapostBridgeLogViewer.APPLY_FILTER', '_Apply Filter'))->addExtraClass('ss-ui-action-constructive')->setUseButtonTag(true),
                                Object::create('ResetFormAction', 'clear', _t('KapostBridgeLogViewer.RESET', '_Reset'))->setUseButtonTag(true)
                            );
        
        
        $form=new Form($this, 'LogsForm', $fields, $actions);
        $form->addExtraClass('cms-search-form')
            ->setFormMethod('GET')
            ->setFormAction($this->Link())
            ->disableSecurityToken()
            ->unsetValidator();
        
        
        // Load the form with previously sent search data
        $form->loadDataFrom($this->request->getVars());
        
        
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
        return KapostBridgeLog::get();
    }
}
?>