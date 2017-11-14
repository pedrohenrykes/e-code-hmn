<?php

class TDashboard extends TElement
{
    
    private $dashboard;
    private $type_dashboard;
    private $image;
    private $color;
    private $text;
    private $text_sub;
    private $functions;
    private $action;

        /**
     * Class Constructor
     * @param  $name name of the field
     */
    public function __construct()
    {
        $this->dashboard = new TElement('div');
        $this->dashboard->{'class'} = 'col-md-4 animated  fadeInRight';
        $this->type_dashboard = 3;
        $this->color = 'bg-blue';
        $this->image = 'fa: fa-thumbs-up';
        $this->text = 'text';
        $this->text_sub = 'text_sub';
    }

    public function setText($value)
    {
        $this->text = $value;
    }

    public function setTextSub($value)
    {
        $this->text_sub = $value;
    }

    public function setType($value)
    {
        $this->type_dashboard = $value;
    }

    public function setImage($img)
    {        
        $this->image = $img;
    }

    public function setColor($value)
    {
        $this->color = $value;
    }

    public function setAction(TAction $action)
    {
        $this->action = $action;
    }

    public function addFunction($function)
    {
        if ($function)
        {
            $this->functions = $function.';';
        }
    }

    public function show()
    {

        $this->actionDash();

        $div_info = new TElement('div');       

        $div_icon = new TElement('div');
        
        $div_icon->add(  new TImage( $this->image ) );

        switch ( $this->type_dashboard ) {
            case 1:

                $div_info->{'class'} = 'info-box hover-zoom-effect';
                $div_icon->{'class'} = 'icon ' . $this->color;   

                break;
            case 2:

                $div_info->{'class'} = 'info-box '. $this->color .' hover-zoom-effect';
                $div_icon->{'class'} = 'icon';  

                break;
            case 3:

                $div_info->{'class'} = 'info-box-2 '. $this->color .' hover-zoom-effect';
                $div_icon->{'class'} = 'icon';    

                break;
            case 4:

                $div_info->{'class'} = 'info-box-3 '. $this->color .' hover-zoom-effect';
                $div_icon->{'class'} = 'icon'; 

                break;
            default:

                $div_info->{'class'} = 'info-box-2 '. $this->color .' hover-zoom-effect';
                $div_icon->{'class'} = 'icon';  

                break;

        }

        $div_content = new TElement('div');
        $div_content->{'class'} = 'content';

        $div_content->add('<div class="text">'  . $this->text_sub .'</div>');
        $div_content->add('<div class="number">'. $this->text     .'</div>');

        $div_info->add(  $div_icon );
        $div_info->add(  $div_content );

        $this->dashboard->add($div_info);
        
        $this->dashboard->show();
    }

    private function actionDash()
    {
        $action = '';

        if ($this->action)
        {
            // get the action as URL
            $url = $this->action->serialize(FALSE);
            if ($this->action->isStatic())
            {
                $url .= '&static=1';
            }

            $wait_message = AdiantiCoreTranslator::translate('Loading');
            $action = "Adianti.waitMessage = '$wait_message';";
            $action.= "{$this->functions}";
            $action.= "__adianti_goto_page('index.php?{$url}');";
            $action.= "return false;";
                        
        }
        else if($this->functions)
        {
            $action = $this->functions;
        }

        $this->dashboard->{'onclick'} = $action;


    }


}