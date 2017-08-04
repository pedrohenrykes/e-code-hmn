<?php

class DashboardForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Cadastro das Opções do Sidemenu" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_dashboard" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id         = new THidden( "id" );
        $quantifier = new TCombo( "quantifier" );
        $dataview   = new TEntry( "dataview" );
        $title      = new TEntry( "title" );
        $color      = new TEntry( "color" );
        $page       = new TEntry( "page" );
        $action     = new TEntry( "action" );

        $icon       = new TDBCombo( "icon", "database", "FontAwesomeModel", "class", "unicode", "id" );
        $icon->style = "font-family:'FontAwesome',Helvetica;font-size:20px";
        $icon->setValue( "fa-500px" );

        $quantifier->addItems( [ "amount"  => "Quantidade", "percent" => "Percentual" ] );
        $quantifier->setDefaultOption( "..::SELECIONE::.." );

        $dataview->forceLowerCase();
        $color->forceLowerCase();

        $dataview->setSize( "38%" );
        $quantifier->setSize( "38%" );
        $title->setSize( "38%" );
        $icon->setSize( "38%" );
        $color->setSize( "38%" );
        $page->setSize( "38%" );
        $action->setSize( "38%" );

        $label01 = new RequiredTextFormat( [ "View de dados", "#F00", "bold" ] );
        $label02 = new RequiredTextFormat( [ "Quantificador", "#F00", "bold" ] );
        $label03 = new RequiredTextFormat( [ "Título", "#F00", "bold" ] );
        $label04 = new RequiredTextFormat( [ "Icone", "#F00", "bold" ] );
        $label05 = new RequiredTextFormat( [ "Cor", "#F00", "bold" ] );

        $dataview->addValidation( $label01->getText(), new TRequiredValidator );
        $quantifier->addValidation( $label02->getText(), new TRequiredValidator );
        $title->addValidation( $label03->getText(), new TRequiredValidator );
        $icon->addValidation( $label04->getText(), new TRequiredValidator );
        $color->addValidation( $label05->getText(), new TRequiredValidator );

        $this->form->addFields( [ new TLabel( "View de dados: $redstar" ) ], [ $dataview ] );
        $this->form->addFields( [ new TLabel( "Quantificador: $redstar" ) ], [ $quantifier ] );
        $this->form->addFields( [ new TLabel( "Título: $redstar" ) ], [ $title ] );
        $this->form->addFields( [ new TLabel( "Icone: $redstar" ) ], [ $icon ] );
        $this->form->addFields( [ new TLabel( "Cor: $redstar" ) ], [ $color ] );
        $this->form->addFields( [ new TLabel( "Página:" ) ], [ $page ] );
        $this->form->addFields( [ new TLabel( "Ação:" ) ], [ $action ] );
        $this->form->addFields( [ $id ] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( new TXMLBreadCrumb( "menu.xml", "DashboardList" ) );
        $container->add( $this->form );

        parent::add( $container );
    }

    public function onSave()
    {
        try {

            $this->form->validate();

            TTransaction::open( "database" );

            $object = $this->form->getData( "DashboardModel" );
            $object->store();

            TTransaction::close();

            $action = new TAction( [ "DashboardList", "onReload" ] );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br>" . $ex->getMessage() );

        }
    }


    public function onEdit( $param )
    {
        try {

            if( isset( $param[ "key" ] ) ) {

                TTransaction::open( "database" );

                $object = new DashboardModel( $param[ "key" ] );

                $this->form->setData( $object );

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}
