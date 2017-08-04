<?php

class ProcedimentoForm extends TPage
{
    private $form;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder( "form_procedimento" );
        $this->form->setFormTitle( "Formulário de procedimentos" );
        $this->form->class = "tform";

        $id  = new THidden( "id" );
        $nomeprocedimento = new TEntry( "nomeprocedimento" );
        
        $nomeprocedimento->setProperty( "title", "O campo é obrigatório" );

        $nomeprocedimento->setSize( "30%" );

        $this->form->addFields( [ new TLabel( "Nome do Procedimento:", "#F00" ) ], [ $nomeprocedimento ] );
        $this->form->addFields( [ $id ] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );
        $this->form->addAction( "Voltar para a listagem", new TAction( [ "ProcedimentoList", "onReload" ] ), "fa:table blue" );

        $container = new TVBox();
        $container->style = "width: 90%";
        //$container->add( new TXMLBreadCrumb( "menu.xml", "ProcedimentoList" ) );
        $container->add( $this->form );

        parent::add( $container );
    }

    public function onSave()
    {
        try {

            $this->form->validate();

            TTransaction::open( "database" );

            $object = $this->form->getData( "ProcedimentoRecord" );

            $object->store();

            TTransaction::close();

            $action = new TAction( [ "ProcedimentoList", "onReload" ] );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );
        }
    }

    public function onEdit( $param )
    {
        try {

            if ( isset( $param[ "key" ] ) ) {

                TTransaction::open( "database" );

                $object = new ProcedimentoRecord( $param[ "key" ] );

                $object->nascimento = TDate::date2br( $object->nascimento );

                $this->form->setData( $object );

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );
        }
    }
}
