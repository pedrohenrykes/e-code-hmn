<?php

class TGroupInputMaterial extends TField implements AdiantiWidgetInterface
{
    private $layout = 'horizontal';
    private $changeAction;
    private $items;
    private $buttons;
    private $labels;
    private $color;
    protected $formName;
    protected $type_input;

    public function __construct($name, $type)
    {
        parent::__construct($name);
        parent::setSize(NULL);   
        $this->type_input = $type;     
    }

    
    public function addItems($items)
    {
        if (is_array($items))
        {
            $this->items = $items;
            $this->buttons = array();
            $this->labels  = array();

            foreach ($items as $key => $value)
            {
                $button = $this->getTypeElement('field', $key);

                $obj   = $this->getTypeElement('label', $key, $value);

                $this->buttons[$key] = $button;
                $this->labels[$key] = $obj;
            }
        }
    }
    
    public function setLayout($dir)
    {
        $this->layout = $dir;
    }

    public function setColorStyle($color)
    {
        $this->color = $color;
    }

    public function setChangeAction(TAction $action)
    {
        if ($action->isStatic())
        {
            $this->changeAction = $action;
        }
        else
        {
            $string_action = $action->toString();
            throw new Exception(AdiantiCoreTranslator::translate('Action (^1) must be static to be used in ^2', $string_action, __METHOD__));
        }
    }
    

    public function show()
    {

        $div = $this->getTypeElement('div');
        
        if ($this->items)
        {
            foreach ($this->items as $index => $label)
            {
                $button = $this->buttons[$index];
                $active = FALSE;

                if( $this->type_input == 'TCheck' ){
                    
                    $button->setName($this->name.'[]');

                    // verify if the checkbutton is checked
                    if (@in_array($index, $this->value) OR $this->allItemsChecked)
                    {
                        $button->setValue($index); // value=indexvalue (checked)
                        $active = TRUE;
                    }

                }else{
                    
                    $button->setName($this->name);     

                    // check if contains any value
                    if ($this->value == $index)
                    {
                        // mark as checked
                        $button->setProperty('checked', '1');
                        $active = TRUE;
                    }           
                }
                
                // create the label for the button
                $obj = $this->labels[$index];
                $obj->{'class'} = $this->labelClass. ($active?'active':'');
                                
                // check whether the widget is non-editable
                if (parent::getEditable())
                {
                    if (isset($this->changeAction))
                    {
                        if (!TForm::getFormByName($this->formName) instanceof TForm)
                        {
                            throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
                        }
                        $string_action = $this->changeAction->serialize(FALSE);
                        
                        $button->setProperty('changeaction', "__adianti_post_lookup('{$this->formName}', '{$string_action}', this, 'callback')");
                        $button->setProperty('onChange', $button->getProperty('changeaction'), FALSE);
                    }
                }
                else
                {
                    $button->setEditable(FALSE);
                    $obj->setFontColor('gray');
                }
                
                $div->add($button);
                $div->add($obj);
                
                if ($this->layout == 'vertical')
                {  
                    $div->add(new TElement('br'));
                }
                
                
            }
        }

        $div->show();
        
    }


    public function getTypeElement($element, $value = null, $text = null)
    {

        $field = '';
        $div   = '';
        $label = '';

        switch ($this->type_input) {

            case 'TRadio':

                $field = new TRadioButton($this->name);
                $field->setProperty('class', 'radio-col-' . $this->color, TRUE);
                $field->setProperty('id', $this->name . $value, TRUE);
                $field->setValue( $value );
                
                $div = new TElement('div');
                $div->class = 'demo-radio-button';
                $div->id = $this->name;

                $label = new TLabel($text);
                $label->{'for'} = $this->name.$value;

                break;
            case 'TCheck':

                $field = new TCheckButton("{$this->name}[]");                
                
                $field->setProperty('class', 'chk-col-' . $this->color, TRUE);
                $field->setProperty('id', $this->name .'[]'. $value, TRUE);
                $field->setProperty('checkgroup', $this->name);

                $field->setIndexValue($value);

                $div = new TElement('div');
                $div->class = 'demo-checkbox';
                $div->id = $this->name;

                $label = new TLabel($text);
                $label->{'for'} = $this->name."[]".$value;
                
                break;
            case 'TSwitch':
                
                break;
            default:
                
                break;
        }

        if($element == 'div')
            return $div;
        else if($element == 'label')
            return $label;
        else
            return $field;
    }


}
