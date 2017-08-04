<?php

class ProfissaoForm extends TWindow
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Formulário Nome de Profissão" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder("form_nome_profissao" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id             = new THidden( "id" );
        $nomeprofissao  = new TEntry( "nomeprofissao" );

        $nomeprofissao->setSize( '38%' );

        $label01 = new RequiredTextFormat( [ "Nome", "#F00", "bold" ] );
        $nomeprofissao->addValidation( $label01->getText(), new TRequiredValidator);

        $this->form->addFields( [ new TLabel( "Profissão: $redstar" ) ], [ $nomeprofissao ] );
        $this->form->addFields( [ $id ] );

        $this->form->addAction( 'Salvar', new TAction( [ $this, 'onSave' ] ), 'fa:save' );

        $container = new TVBox();
        $container->style = "width: 100%";
        //$container->add( new TXMLBreadCrumb( "menu.xml", "ProfissaoList" ) );
        $container->add( $this->form );

        parent::add( $container );
    }

    public function onSave()
    {
        try {

            $this->form->validate();

            TTransaction::open( 'database' );

            $object = $this->form->getData( 'ProfissaoRecord' );

            $object->store();

            TTransaction::close();

            $action = new TAction( [ 'ProfissaoList', 'onReload' ] );

            new TMessage( 'info', 'Registro salvo com sucesso!', $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( 'error', 'Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>' . $ex->getMessage() );
        }
    }

    public function onEdit( $param )
    {
        try {

            if( isset( $param[ 'key' ] ) ) {

                TTransaction::open( 'database' );

                $object = new ProfissaoRecord( $param[ 'key' ] );

                TTransaction::close();

                $this->form->setData( $object );
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( 'error', 'Ocorreu um erro ao tentar carregar o registro para edição!<br><br>' . $ex->getMessage() );
        }
    }
}
