<?php


use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Form\TField;


class TDialogIcon extends TField implements AdiantiWidgetInterface
{
     
    public function show()
    {
        // set the tag properties
        $this->tag->{'id'}    = 'icon_value';
        $this->tag->{'name'}  = $this->name;  
        $this->tag->{'value'} = $this->value; // tag value
        $this->tag->{'type'}  = 'text';     // input type
        $this->tag->{'style'} = "width:{$this->size}";
        

        $a = new TElement('a');
        $a->type = "button";
        $a->class = "btn btn-default waves-effect m-r-20";
        $a->id = "icon_a";
        $a->{'data-toggle'} = "modal";
        $a->{'data-targe'} = "#largeModal";        
        $a->{'onclick'} = "create_icon_model('".TSession::getValue('theme')."')";
        $a->{'style'} = "position: absolute; margin-left: 5px;  margin-top: -4px;";        

        $a->add('<i style="top: 5px;" class="fa fa-fw fa-image"></i>');    
    
        if($this->value){

            TScript::create( "loadImage( '{$this->value}' );");

        }


        $this->tag->add($a);
        $this->tag->show();
    }
}