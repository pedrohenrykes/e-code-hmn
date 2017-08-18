<?php

class SideMenuForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Cadastro de Itens do Sidemenu" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_sidemenu" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id           = new THidden( "id" );
        $menu_type    = new TCombo( "menu_type" );
        $name         = new TEntry( "name" );
        $action_class = new TMultiSearch( "action_class" );
        $sequence     = new TEntry( "sequence" );

        $criteria = new TCriteria();
        $criteria->add( new TFilter( "menu_type", "=", "menu" ) );
        $menu_id = new TDBCombo( "menu_id", "database", "SideMenuModel", "id", "name", "name", $criteria );

        $icon = new TDBCombo( "icon", "database", "FontAwesomeIconsModel", "class", "unicode", "id" );
        $icon->style = "font-family:'FontAwesome',Helvetica;font-size:20px";
        $icon->setValue( "fa-500px" );

        $menu_type->setChangeAction( new TAction( [ $this, 'onChangeAction' ] ) );
        $menu_type->addItems( [ "menu" => "Menu", "submenu" => "Sub-Menu" ] );
        // $menu_type->setValue( "menu" );

        $menu_id->setDefaultOption( "..::SELECIONE::.." );
        $menu_type->setDefaultOption( "..::SELECIONE::.." );
        $sequence->setProperty("placeholder", "Apenas números naturais ou decimais");

        $action_class->addItems( $this->getPageClasses() );
        $action_class->setMaxSize(1);
        $action_class->setMinLength(0);

        $menu_type->setSize( "38%" );
        $name->setSize( "38%" );
        $icon->setSize( "38%" );
        $sequence->setSize( "38%" );
        $action_class->setSize( "38%" );
        $menu_id->setSize( "38%" );

        $label01 = new RequiredTextFormat( [ "Tipo", "#F00", "bold" ] );
        $label02 = new RequiredTextFormat( [ "Nome", "#F00", "bold" ] );

        $menu_type->addValidation( $label01->getText(), new TRequiredValidator );
        $name->addValidation( $label02->getText(), new TRequiredValidator );

        $this->form->addFields( [ new TLabel( "Tipo: $redstar") ], [ $menu_type ] );
        $this->form->addFields( [ new TLabel( "Nome: $redstar") ], [ $name ] );
        $this->form->addFields( [ new TLabel( "Icone:") ], [ $icon ] );
        $this->form->addFields( [ new TLabel( "Sequência:") ], [ $sequence ] );
        $this->form->addFields( [ new TLabel( "Classe:") ], [ $action_class ] );
        $this->form->addFields( [ new TLabel( "Menu:") ], [ $menu_id ] );
        $this->form->addFields( [ $id ] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );

        parent::add( $container );
    }

    public function onSave()
    {
        $object = $this->form->getData( "SideMenuModel" );

        try {

            $this->form->validate();

            TTransaction::open( "database" );

            $object->active = "Y";
            $object->sequence = str_replace( ",", ".", $object->sequence );
            $object->action_class = reset( $object->action_class );

            if ( empty( $object->menu_id ) ) {
                $object->menu_id = 0;
            }

            $object->store();

            TTransaction::close();

            TWindow::closeWindow();

            $action = new TAction( [ "SideMenuList", "onReload" ] );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            $this->form->setData( $object );

            self::onChangeAction( [ "key" => $object->menu_type ] );

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );
        }
    }

    public function onEdit( $param )
    {
        try {

            if ( isset( $param[ "key" ] ) ) {

                TTransaction::open( "database" );

                $object = new SideMenuModel( $param[ "key" ] );

                $object->action_class = [ $object->action_class, $object->action_class ];

                TTransaction::close();

                $this->form->setData( $object );

                self::onChangeAction( [ "key" => $object->menu_type ] );

            } else {

                self::onChangeAction( [ "key" => "menu" ] );
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );
        }
    }

    public static function onChangeAction( $param )
    {
        $object = new StdClass;
        $object->menu_id = "";
        $object->action_class = [ "", "" ];

        switch ( $param[ "key" ] ) {

            case "menu":

                TQuickForm::hideField("form_sidemenu", "action_class");
                TQuickForm::hideField( "form_sidemenu", "menu_id" );
                TQuickForm::sendData( "form_sidemenu", $object );

                break;

            case "submenu":

                TQuickForm::showField( "form_sidemenu", "action_class" );
                TQuickForm::showField( "form_sidemenu", "menu_id" );

                break;

        }
    }

    private function getPageClasses()
    {
        $entries = [];

        foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( "app/control" ),
            RecursiveIteratorIterator::CHILD_FIRST) as $arquivo )
        {

            if ( substr( $arquivo, -4 ) == ".php" ) {

                $name = $arquivo->getFileName();

                $pieces = explode( '.', $name );

                $class = (string) $pieces[ 0 ];

                $entries[ $class ] = $class;

            }

        }

        ksort( $entries );

        return $entries;
    }
}
