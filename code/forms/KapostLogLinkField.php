<?php
class KapostLogLinkField extends FormField {
    protected $allowHTML=true;
    
    protected $_link;
    protected $_label;
    
    /**
     * Constructor
     * @param string $name Name of the field
     * @param string $title Title for use on the field
     * @param string $link Link target to use
     * @param string $label Link label to use
     */
    public function __construct($name, $title=null, $link=null, $label=null) {
        parent::__construct($name, $title);
        
        $this->_link=$link;
        $this->_label=$label;
    }
    
    /**
     * Sets the label to use in the link
     * @param string $value
     */
    public function setLinkLabel($value) {
        $this->_label=$value;
        return $this;
    }
    
    /**
     * Gets the label to used in the link
     * @return string
     */
    public function getLinkLabel() {
        return $this->_label;
    }
    
    /**
     * Sets the target of the link
     * @param string $value
     */
    public function setLinkTarget($value) {
        $this->_link=$value;
        return $this;
    }
    
    /**
     * Gets the target of the link
     * @return string
     */
    public function getLinkTarget() {
        return $this->_link;
    }
    
    /**
     * Generates the contents of the field
     * @param array $properties Properties to add to the field (not used)
     * @return string
     */
    public function Field($properties=null) {
        if(!empty($this->_link) && !empty($this->_label)) {
            return '<span id="'.$this->ID().'" class="readonly"><a href="'.Convert::raw2att($this->_link).'" class="cms-panel-link">'.Convert::raw2xml($this->_label).'</a></span>';
        }
    }
    
    /**
     * Generates the normal field holder for the field
     * @param array $properties Properties to add to the field holder
     * @return string
     */
    public function FieldHolder($properties=null) {
        if(!empty($this->_link) && !empty($this->_label)) {
            return parent::FieldHolder($properties);
        }
    }
    
    /**
     * Generates the small field holder for the field
     * @param array $properties Properties to add to the field holder
     * @return string
     */
    public function SmallFieldHolder($properties=null) {
        if(!empty($this->_link) && !empty($this->_label)) {
            return parent::SmallFieldHolder($properties);
        }
    }
    
    /**
     * This field has no data so return false
     * @return bool
     */
    public function hasData() {
        return false;
    }
    
    /**
     * Returns a readonly version of this field
     */
    public function performReadonlyTransformation() {
        $clone=clone $this;
        $clone->setReadonly(true);
        
        return $clone;
    }
    
    /**
     * Type of the field is readonly
     * @return string
     */
    public function Type() {
        return 'readonly';
    }
}
?>