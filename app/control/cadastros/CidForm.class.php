<?php

// Revisado 18.05.17

class CidForm extends TPage
{
    private $form;

    public function __construct()
    {
        parent::__construct();

        //Criacao do formulario
        $this->form = new BootstrapFormBuilder( "form_cid" );
        $this->form->setFormTitle( "Formulário de C.I.D." );
        $this->form->class = "tform";

        //Criacao dos campos do fomulario
        $id = new THidden( "id" );
        $codigocid = new TEntry( "codigocid" );
        $nomecid = new TEntry( "nomecid" );

        //definicao de tipo de caixa das letras
        $codigocid->forceUpperCase();
        //$nomecid->forceUpperCase();

        //Definicao de propriedades dos campos
        $codigocid->setProperty('title', 'O campo e obrigatorio');
        $nomecid->setProperty('title', 'O campo e obrigatorio');

        $codigocid->setSize('38%');
        $nomecid->setSize('38%');

        //Definicao de campos obrigatorios e requeridos especiais
        $codigocid->addValidation( "Código", new TRequiredValidator );
        $nomecid->addValidation( "Nome", new TRequiredValidator );

        //Insercao dos campos no formulario
        $this->form->addFields([new TLabel('Código<font color=red>*</font>')], [$codigocid]);
        $this->form->addFields([new TLabel('Nome<font color=red>*</font>')], [$nomecid]);
        $this->form->addFields( [ $id ] );

        //Criacao dos botoes com sua determinada acoes no fomulario
        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );
        $this->form->addAction( "Voltar para a listagem", new TAction( [ "CidList", "onReload" ] ), "fa:table blue" );

        //Criacao do container que recebe o formulario
        $container = new TVBox();
        $container->style = "width: 90%";
        $container->add( new TXMLBreadCrumb( "menu.xml", "CidList" ) );
        $container->add( $this->form );
        parent::add( $container );
    }
    public function onSave()
    {
        try
        {
            //Validacao do formulario
            $this->form->validate();

            TTransaction::open( "database" );

            $object = $this->form->getData('CidRecord');
            $object->store();

            TTransaction::close();

            $action = new TAction( [ "CidList", "onReload" ] );
            new TMessage( "info", "Registro salvo com sucesso!", $action );
        }
        catch ( Exception $ex )
        {
            TTransaction::rollback();
            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br>" . $ex->getMessage() );
        }
    }
    public function onEdit( $param )
    {
        try
        {
            if( isset( $param[ "key" ] ) )
            {
                TTransaction::open( "database" );
                $object = new CidRecord($param['key']);
                $this->form->setData($object);

                TTransaction::close();
            }
        }
        catch ( Exception $ex )
        {
            TTransaction::rollback();
            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );
        }
    }
}
