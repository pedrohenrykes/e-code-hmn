<?php

class SystemUserForm extends TPage
{
    protected $form;
    protected $program_list;

    function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder( 'form_System_user' );
        $this->form->setFormTitle( _t( 'User' ) );

        $id                = new THidden( 'id' );
        $name              = new TEntry( 'name' );
        $login             = new TEntry( 'login' );
        $password          = new TPassword( 'password' );
        $repassword        = new TPassword( 'repassword' );
        $email             = new TEntry( 'email' );
        $program_id        = new TDBSeekButton( 'program_id', 'database', 'form_System_user', 'SystemProgram', 'name', 'program_id', 'program_name' );
        $program_name      = new TEntry( 'program_name' );
        $groups            = new TDBCheckGroup( 'groups','database','SystemGroup','id','name' );
        $profissional_id   = new TDBSeekButton( 'profissional_id', 'database', 'form_System_user', 'ProfissionalRecord', 'nomeprofissional', 'profissional_id', 'profissional_name' );
        $profissional_name = new TEntry( 'profissional_name' );
        // $unit_id             = new TDBCombo( 'system_unit_id','database','SystemUnit','id','name' );
        // $frontpage_id        = new TDBSeekButton( 'frontpage_id', 'database', 'form_System_user', 'SystemProgram', 'name', 'frontpage_id', 'frontpage_name' );
        // $frontpage_name      = new TEntry( 'frontpage_name');

        $groups->setLayout( 'horizontal' );
        $groups->setBreakItems( 3 );
        $name->forceUpperCase();

        foreach ( $groups->getLabels() as $label ) {
            $label->setSize( 120 );
        }

        $frame_groups = new TFrame( NULL, 160 );
        $frame_groups->setLegend( _t( 'Groups' ) );
        $frame_groups->style .= ';margin:0px;width:95%';

        $frame_programs = new TFrame( NULL, 280 );
        $frame_programs->setLegend( _t( 'Programs' ) );
        $frame_programs->style .= ';margin:0px;width:95%';

        $this->form->addAction( _t( 'Save' ), new TAction( [ $this, 'onSave' ] ), 'fa:floppy-o' );
        $this->form->addAction( _t( 'New' ), new TAction( [ $this, 'onEdit' ] ), 'fa:eraser red' );
        $this->form->addAction( _t( 'Back to the listing' ), new TAction( [ 'SystemUserList','onReload' ] ), 'fa:table blue' );

        $add_button = TButton::create( 'add', [ $this,'onAddProgram' ], _t( 'Add' ), 'fa:plus green' );

        $this->form->addField( $groups );
        $this->form->addField( $program_id );
        $this->form->addField( $program_name );
        $this->form->addField( $add_button );

        $frame_groups->add( $groups );

        $this->program_list = new TQuickGrid;
        $this->program_list->setHeight( 180 );
        $this->program_list->makeScrollable();
        $this->program_list->style = 'width: 100%';
        $this->program_list->id = 'program_list';
        $this->program_list->disableDefaultClick();
        $this->program_list->addQuickColumn( '', 'delete', 'center', '5%' );
        $this->program_list->addQuickColumn( 'Id', 'id', 'left', '10%' );
        $this->program_list->addQuickColumn( _t( 'Program' ), 'name', 'left', '85%' );
        $this->program_list->createModel();

        $hbox = new THBox;
        $hbox->add( $program_id );
        $hbox->add( $program_name, 'display:initial' );
        $hbox->add( $add_button );
        $hbox->style = 'margin: 4px';

        $vbox = new TVBox;
        $vbox->style = 'width:100%';
        $vbox->add( $hbox );
        $vbox->add( $this->program_list );
        $frame_programs->add( $vbox );

        $name->setSize( '38%' );
        $login->setSize( '38%' );
        $password->setSize( '38%' );
        $repassword->setSize( '38%' );
        $email->setSize( '38%' );
        $program_id->setSize( '30' );
        $program_name->setSize( 'calc(50% - 200px)' );
        $profissional_id->setSize( '60' );
        $profissional_name->setSize( 'calc(38% - 60px)' );
        // $unit_id->setSize('38%');
        // $frontpage_id->setSize('60');
        // $frontpage_name->setSize('calc(38% - 60px)');

        $program_name->setEditable( false );
        $profissional_name->setEditable( false );
        // $frontpage_name->setEditable( false );

        $name->addValidation( _t('Name'), new TRequiredValidator );
        $login->addValidation( 'Login', new TRequiredValidator );
        $email->addValidation( 'Email', new TEmailValidator );
        $profissional_id->addValidation( _t( 'Profissional' ), new TRequiredValidator );

        $this->form->addFields( [ new TLabel( 'Profissional' ) ], [ $profissional_id, $profissional_name ] );
        $this->form->addFields( [ new TLabel( _t( 'Name' ) ) ], [ $name ] );
        $this->form->addFields( [ new TLabel( _t( 'Login' ) ) ], [ $login ] );
        $this->form->addFields( [ new TLabel( _t( 'Password' ) ) ], [ $password ] );
        $this->form->addFields( [ new TLabel( _t( 'Password confirmation' ) ) ], [ $repassword ] );
        $this->form->addFields( [ new TLabel( _t( 'Email' ) ) ], [ $email ] );
        // $this->form->addFields( [ new TLabel( _t( 'Unit' ) ) ], [ $unit_id ] );
        // $this->form->addFields( [ new TLabel( _t( 'Front page' ) ) ], [ $frontpage_id, $frontpage_name ] );
        $this->form->addFields( [ $id ] );

        $this->form->addContent( [ $frame_groups ] );
        $this->form->addContent( [ $frame_programs ] );

        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add( $this->form );

        parent::add( $container );
    }

    public static function deleteProgram( $param )
    {
        $programs = TSession::getValue( 'program_list' );
        unset($programs[ $param['id'] ]);
        TSession::setValue( 'program_list', $programs );
    }

    public static function onSave( $param )
    {
        try {

            TTransaction::open( 'database' );

            $object = new SystemUser;
            $object->fromArray( $param );

            $senha = $object->password;

            if( empty( $object->login ) ) {
                throw new Exception( TAdiantiCoreTranslator::translate( 'The field ^1 is required', _t( 'Login' ) ) );
            }

            if( empty( $object->id ) ) {

                if ( SystemUser::newFromLogin( $object->login ) instanceof SystemUser ) {
                    throw new Exception( _t( 'An user with this login is already registered' ) );
                }

                if ( empty( $object->password ) ) {
                    throw new Exception(TAdiantiCoreTranslator::translate( 'The field ^1 is required', _t( 'Password' ) ) );
                }

                $object->active = 'Y';
            }

            if( $object->password ) {

                if( $object->password !== $param['repassword'] ) {
                    throw new Exception( _t( 'The passwords do not match' ) );
                }

                $object->password = md5( $object->password );

            } else {

                unset( $object->password );

            }

            if ( empty( $object->name ) ) {
                $object->name = $param['profissional_name'];
            }

            $object->store();
            $object->clearParts();

            if( !empty( $param['groups'] ) ) {
                foreach( $param['groups'] as $group_id ) {
                    $object->addSystemUserGroup( new SystemGroup( $group_id ) );
                }
            }

            $programs = TSession::getValue( 'program_list' );

            if ( !empty( $programs ) ) {
                foreach ( $programs as $program ) {
                    $object->addSystemUserProgram( new SystemProgram( $program['id'] ) );
                }
            }

            $data = new stdClass;
            $data->id = $object->id;

            TForm::sendData( 'form_System_user', $data );

            TTransaction::close();

            $action = new TAction( [ "SystemUserList", "onReload" ] );
            new TMessage( 'info', TAdiantiCoreTranslator::translate( 'Record saved' ), $action );

        } catch ( Exception $e ) {

            new TMessage( 'error', $e->getMessage() );

            TTransaction::rollback();

        }
    }

    function onEdit( $param )
    {
        try {

            if ( isset( $param['key'] ) ) {

                $key = $param['key'];

                TTransaction::open( 'database' );

                $object = new SystemUser( $key );
                $object->profissional_name = $object->nomeprofissional;
                unset( $object->password );

                $groups = [];

                if( $groups_db = $object->getSystemUserGroups() ) {
                    foreach( $groups_db as $grup ) {
                        $groups[] = $grup->id;
                    }
                }

                $data = [];

                foreach ( $object->getSystemUserPrograms() as $program ) {

                    $data[ $program->id ] = $program->toArray();
                    $item = new stdClass;
                    $item->id = $program->id;
                    $item->name = $program->name;
                    $i = new TElement( 'i' );
                    $i->{'class'} = 'fa fa-trash red';
                    $btn = new TElement( 'a' );
                    $btn->{'onclick'} = "__adianti_ajax_exec('class=SystemUserForm&method=deleteProgram&id={$program->id}');$(this).closest('tr').remove();";
                    $btn->{'class'} = 'btn btn-default btn-sm';
                    $btn->add( $i );
                    $item->delete = $btn;
                    $tr = $this->program_list->addItem( $item );
                    $tr->{'style'} = 'width: 100%;display: inline-table;';

                }

                $object->groups = $groups;

                $this->form->setData( $object );

                TTransaction::close();

                TSession::setValue( 'program_list', $data );

            } else {

                $this->form->clear();

                TSession::setValue( 'program_list', null );

            }

        } catch ( Exception $e )  {

            new TMessage( 'error', $e->getMessage() );

            TTransaction::rollback();

        }
    }

    public static function onAddProgram( $param )
    {
        try {

            $id = $param[ 'program_id' ];
            $program_list = TSession::getValue( 'program_list' );

            if ( !empty( $id ) AND empty( $program_list[ $id ] ) ) {

                TTransaction::open( 'database' );

                $program = SystemProgram::find( $id );
                $program_list[ $id ] = $program->toArray();

                TSession::setValue( 'program_list', $program_list );

                TTransaction::close();

                $i = new TElement( 'i' );
                $i->{'class'} = 'fa fa-trash red';
                $btn = new TElement( 'a' );
                $btn->{'onclick'} = "__adianti_ajax_exec(\'class=SystemGroupForm&method=deleteProgram&id=$id\');$(this).closest(\'tr\').remove();";
                $btn->{'class'} = 'btn btn-default btn-sm';
                $btn->add( $i );
                $tr = new TTableRow;
                $tr->{'class'} = 'tdatagrid_row_odd';
                $tr->{'style'} = 'width: 100%;display: inline-table;';
                $cell = $tr->addCell( $btn );
                $cell->{'style'}='text-align:center';
                $cell->{'class'}='tdatagrid_cell';
                $cell->{'width'} = '5%';
                $cell = $tr->addCell( $program->id );
                $cell->{'class'}='tdatagrid_cell';
                $cell->{'width'} = '10%';
                $cell = $tr->addCell( $program->name );
                $cell->{'class'}='tdatagrid_cell';
                $cell->{'width'} = '85%';
                TScript::create( "tdatagrid_add_serialized_row('program_list', '$tr');" );
                $data = new stdClass;
                $data->program_id = '';
                $data->program_name = '';
                TForm::sendData( 'form_System_user', $data );

            }

        } catch ( Exception $e ) {

            new TMessage( 'error', $e->getMessage() );

        }
    }
}
