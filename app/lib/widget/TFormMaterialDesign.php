<?php

//ini_set('display_errors',1);
//ini_set('display_startup_erros',1);
//error_reporting(E_ALL);

class TFormMaterialDesign extends TForm
{
    protected $form_content; 
    protected $row;
    protected $form_name;
    protected $campo;
    protected $div_element;
    private $title;
    private $tab_header;
    private $tab_content;
    private $tab_body;
    private $number_tab;
    private $current_page;
    private $tab_active;

    public function __construct($name = 'my_form')
    {
        parent::__construct($name);
        $this->form_name = $name;
        $this->setProperty('class', 'panel panel-default', true);

        //Div form
        $this->form_content = new TElement('div');
        $this->form_content->style = "padding: 0px 15px;";

        //Div content_form
        $this->div_element = new TElement('div');

        $this->tab_create();

        TScript::create(" $.AdminBSB.input.activate(); ");

    }

    public function show()
    {
        if (!empty($this->title))
        {
            $heading = new TElement('div');
            $heading->{'class'} = 'panel-heading animated  pulse';
            $heading->{'style'} = 'width: 100%;height:43px;padding:5px;';
            $heading->add(TElement::tag('div', $this->title, ['class'=>'panel-title', 'style'=>'padding:5px;float:left;font-weight: bold;color:#3f51b5;']));
            
            parent::add($heading);
        }

        $this->form_content->add($this->tab_header);
        $this->form_content->add($this->tab_content);
        $this->form_content->add($this->div_element);

        parent::add($this->form_content);

        parent::show();

        $this->getStyleScript();

    }


    public function setFormTitle($title)
    {
        $this->title = $title;
    }

    public function tab_create()
    {
        $this->current_page = 0;
        $this->number_tab = 0;
        
        $this->tab_header = new TElement('ul');        
        $this->tab_header->{'role'} = 'tablist'; 

        $this->tab_content = new TElement('div');
        $this->tab_content->class = 'tab-content';
    }

    public function setCurrentPage($i)
    {
        $this->current_page = $i;
    }

    public function tab($tab_page, $color = 'black')
    {

        $li = new TElement('li');
        $li->{'role'} = 'presentation';  

        $a = new TElement('a');                 
        $a->{'data-toggle'} = 'tab';  
        $a->{'href'} = '#'. strtolower(str_replace(" ","",$tab_page));
        $a->{'style'} = 'color: ' . $color . ' !important';

        $this->tab_body = new TElement('div');           
        $this->tab_body->{'role'} = 'tabpanel'; 
        $this->tab_body->{'class'} = 'tab-pane fade';
        $this->tab_body->{'id'} = strtolower(str_replace(" ","",$tab_page));

        if( $this->current_page == $this->number_tab ){
            $a->{'class'} = 'active';
            $li->{'class'} = 'active';
            $this->tab_body->{'class'} = 'tab-pane fade in active';
        }

        $a->add($tab_page);
        $li->add($a);   
        
        $this->tab_header->add( $li );
        $this->tab_header->{'class'} = 'nav nav-tabs tab-nav-right tab-col-' . $color;
        $this->tab_content->add( $this->tab_body );

        $this->number_tab++;
        $this->tab_active = true;

        return $this;

    }

    public function divRow( $title = null, $className = '')
    {

        $this->row = new TElement('div');
        $this->row->class = 'row clearfix button-demo' . $className;

        if($title){

            $h = new TElement('h5');
            $h->style = "margin-top: 0px; margin-bottom: -15px;";
            $h->add($title);

            $this->form_content->add($h);
        }

        if( $this->tab_active ){

            $this->tab_body->add($this->row);
            $this->tab_active = false;

        }else{           

            $this->div_element->add($this->row);
        }

        return $this;
    }

    public function mountField( AdiantiWidgetInterface $campo = null, $text = null, $classCol = 'col-xs-12 col-sm-12 col-md-12 col-lg-8' )
    {
    
        $this->campo = $campo;
        
        $div_col = new TElement('div');
        $div_col->class =  $classCol;
        $div_col->style = "margin-bottom: 18px";

        $div_group = new TElement('div');
        $div_group->class = "form-group form-float";
        $div_group->style = "margin-bottom: 0px; margin-top: 0px";

        $div_field = new TElement('div');
        
        if($this->campo instanceof TField){

            $this->campo->setId($this->campo->getName());

            if($this->campo instanceof THidden){

                $div_col->style = "display: none;";
                $div_field->add($this->campo);

            }else if( $this->campo instanceof TRadioGroup || $this->campo instanceof TCheckGroup ){
 
                if($this->campo->getLayout() != 'vertical'){                    
                    $this->campo->setSize('0');                                        
                }
                      
                $div_field->add('<label style="color: #aaa; margin-bottom: 5px; width: 100%;">'. $text .'</label>');
                $div_field->add( $this->campo);

            }else if( $this->campo instanceof TSeekButton){

                $this->campo->setSize('100%'); 
                $div_field->add('<label style="color: #aaa; margin-bottom: 5px; width: 100%;">'. $text .'</label>');
                $div_field->add( $this->campo);
                
            }else if( $this->campo instanceof TEntry  ){

                $div_field->class= "form-line";
                $this->campo->class = 'form-control';
                
                $div_field->add($this->campo);
                $div_field->add('<label class="form-label" id="lb_'. $this->campo->getId() .'" for="'. $this->campo->getId() .'">'. $text .'</label>');     
           
            }else if( $this->campo instanceof TCombo  ){

                $div_field->class= "form-line";
                $this->campo->setSize('100%'); 
                $div_field->add('<label style="color: #aaa; margin-bottom: 5px; width: 100%;">'. $text .'</label>');
                $div_field->add( $this->campo);   
           
            }else{

                $div_field->class= "form-line";
                $this->campo->setSize('100%'); 
                $div_field->add('<label style="color: #aaa; margin-bottom: 5px; width: 100%;">'. $text .'</label>');
                $div_field->add( $this->campo);

            }

           parent::addField($this->campo);
    
        }else{

            $div_field->add($this->campo);
            if($this->campo == null)
            $div_col->style = "margin-bottom: 0px; margin-top: 0px";

        }

        $div_group->add( $div_field );

        $div_col->add( $div_group );

        $this->row->add(  $div_col );

        return $this;

    }

    public function isRequired()
    {
        if($this->campo instanceof TField)
            $this->campo->{'required'} = true;           
        
        return $this;
    }

    public function addAction($label, TAction $action, $icon = 'mi:save', $type = 'btn-m-primary')
    {       

        $label_info = ($label instanceof TLabel) ? $label->getValue() : $label;
        $name   = strtolower(str_replace(' ', '_', $label_info));
        $button = new TButton($name);
        $button->class = "btn {$type} waves-effect";
        $button->type = "submit";
        $button->setAction($action, $label);
        $button->setImage($icon);
        $button->style = "margin-left: 15px; margin-top: 1px; padding-right: 15px;";
        
        parent::addField($button);

        $this->row->add(  $button );

        return $this;
    }

    public function getFormValidation()
    {

        $this->setData($this->getData());
        
        $errors = array();

        foreach ($this->fields as $fieldObject)
        {
            if( !$fieldObject instanceof TButton ){

                if( $fieldObject->{'required'} ){
                 
                    if( $fieldObject->getValue() == NULL && $fieldObject->getValue() == '' ){

                        $errors[] = $fieldObject->getName();
                    }

                }

            }

        }
        
        //var_dump($errors);

        $errors = json_encode( $errors );

        if (count($errors) > 0)
        {
            TScript::create(" form_validation({$errors}); ");
        }
    }

    public function getStyleScript()
    {
 
        $style1 = new TStyle('tcheckgroup_label');
        $style1->{'padding-right'} = '20px';        
        $style1->show();

        $style2 = new TStyle('tseek-group');
        $style2->{'display'} = 'flex !important';        
        $style2->{'flex-direction'} = 'row-reverse';        
        $style2->show();

        $style3 = new TStyle('tseekbutton');
        $style3->{'min-width'} = '60px !important';  
        $style3->{'border-radius'} = '0px';
        $style3->show();

        $style3 = new TStyle('tseekentry');
        $style3->{'min-width'} = '60px';  
        $style3->{'border-color'} = '#ddd';
        $style3->{'border-radius'} = '0px';
        $style3->{'border-right'} = '0px';
        $style3->{'border-left'} = '0px';
        $style3->{'border-top'} = '0px';
        $style3->show();

        $style4 = new TStyle('tfield');
        $style4->{'box-shadow'} = 'none';          
        $style4->show();

        $style5 = new TStyle('fa-search');
        $style5->{'padding-right'} = '0px'; 
        $style5->{'margin-top'} = '7px'; 
        $style5->show();

        $style6 = new TStyle('tcombo');
        $style6->{'border'} = '0px'; 
        $style6->show();

    }

    public static function showField($form, $field)
    {
        TScript::create("tformdesign_show_field('{$form}', '{$field}')");
    }
    
    public static function hideField($form, $field)
    {
        TScript::create("tformdesign_hide_field('{$form}', '{$field}')");
    }


}
