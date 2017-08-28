<?php

class SistemaCorporeoForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Sistemas Corpóreos" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_sistema_corporeo" );
        $this->form->setFormTitle( "({$redstar}) campos obrigatórios" );
        $this->form->class = "tform";

        $id                  = new THidden( "id" );
        $nomesistemacorporeo = new TEntry( "nomesistemacorporeo" );
        $nomesistemacorporeo->setProperty("title", "Digite o nome do sistema");
        $nomesistemacorporeo->setSize("38%");

        $this->form->addFields( [ new TLabel( "Nome: {$redstar}" ) ], [ $nomesistemacorporeo ] );
        $this->form->addFields( [ $id ] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );

        parent::add( $container );
    }

    public function onSave()
    {
        try {

            $this->form->validate();

            TTransaction::open( "database" );

            $object = $this->form->getData("SistemaCorporeoRecord");

            $object->store();

            TTransaction::close();

            $action = new TAction( [ "SistemaCorporeoList", "onReload" ] );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }
    }

    public function onEdit( $param )
    {
        try {

            if( isset( $param[ "key" ] ) ) {

                TTransaction::open( "database" );

                $object = new SistemaCorporeoRecord( $param["key"] );

                $this->form->setData( $object );

                TTransaction::close();

            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}
