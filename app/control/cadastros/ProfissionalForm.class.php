<?php

class ProfissionalForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Cadastro de Profissionais" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_profissional" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id  = new THidden( "id" );
        $tipoprofissional_id = new TDBCombo( "tipoprofissional_id ", "database", "TipoProfissionalRecord", "id", "nometipoprofissional", "nometipoprofissional" );
        $nomeprofissional  = new TEntry( "nomeprofissional" );
        $numeroconselho  = new TEntry( "numeroconselho" );
        $numerocpf = new TEntry("numerocpf");
        $endereco = new TEntry("endereco");
        $bairro = new TEntry("bairro");
        $cidade = new TEntry("cidade");
        $uf = new TDBCombo ( "uf", "database", "EstadosRecord", "sigla", "estado", "estado" );
        $telefone1 = new TEntry("telefone1");
        $telefone2 = new TEntry("telefone2");
        $email = new TEntry("email");
        $situacao = new TCombo('situacao');


        //$nomeprofissional->forceUpperCase();
        //$numeroconselho->setMask( "A!" );

        $nomeprofissional->setProperty("title", "O campo e obrigatorio");
        $tipoprofissional_id ->setProperty("title", "O campo e obrigatorio");
        $numeroconselho ->setProperty("title", "O campo e obrigatorio");
        $numerocpf ->setProperty("title", "O campo e obrigatorio");
        $endereco ->setProperty("title", "O campo e obrigatorio");
        $bairro ->setProperty("title", "O campo e obrigatorio");
        $cidade ->setProperty("title", "O campo e obrigatorio");
        $uf ->setProperty("title", "O campo e obrigatorio");
        $telefone1 ->setProperty("title", "O campo e obrigatorio");
        $telefone2 ->setProperty("title", "O campo e obrigatorio");
        $email ->setProperty("title", "O campo e obrigatorio");
        $situacao ->setProperty("title", "O campo e obrigatorio");

        $tipoprofissional_id->setSize("38%");
        $nomeprofissional->setSize("38%");
        $numeroconselho->setSize("38%");
        $numerocpf->setSize("38%");
        $endereco->setSize("38%");
        $bairro->setSize("38%");
        $cidade->setSize("38%");
        $uf->setSize("38%");
        $telefone1->setSize("38%");
        $telefone2->setSize("38%");
        $email->setSize("38%");
        $situacao->setSize("38%");

        $situacao->setDefaultOption('::..SELECIONE..::');
        $situacao->addItems([ 'ATIVO'=>'ATIVO','INATIVO'=>'INATIVO']);

        $cidade->setValue( "NATAL" );
        $uf->setValue( "RN" );

        $numerocpf->setMask( "999.999.999-99" );
        $telefone1->setMask( "(99)9999-9999" );
        $telefone2->setMask( "(99)9999-9999" );

        $nomeprofissional->forceUpperCase();
        $numeroconselho->forceUpperCase();
        $endereco->forceUpperCase();
        $bairro->forceUpperCase();
        $cidade->forceUpperCase();

        $nomeprofissional->addValidation( TextFormat::set( "Nome do Profissional" ), new TRequiredValidator );
        $tipoprofissional_id->addValidation( TextFormat::set( "Tipo de Profissional" ), new TRequiredValidator );
        $numeroconselho->addValidation( TextFormat::set( "Numero do Conselho" ), new TRequiredValidator );
        $numerocpf->addValidation( TextFormat::set( "CPF" ), new TRequiredValidator );
        $endereco->addValidation( TextFormat::set( "Endereço" ), new TRequiredValidator );
        $bairro->addValidation( TextFormat::set( "Bairro" ), new TRequiredValidator );
        $cidade->addValidation( TextFormat::set( "Cidade" ), new TRequiredValidator );
        $uf->addValidation( TextFormat::set( "UF" ), new TRequiredValidator );
        $telefone1->addValidation( TextFormat::set( "Telefone" ), new TRequiredValidator );
        $telefone2->addValidation( TextFormat::set( "Telefone" ), new TRequiredValidator );
        $email->addValidation( TextFormat::set( "E-mail" ), new TRequiredValidator );
        $situacao->addValidation( TextFormat::set( "Situacao" ), new TRequiredValidator );

        $this->form->addFields([new TLabel("Nome do Profissional: $redstar")], [$nomeprofissional]);
        $this->form->addFields([new TLabel("Tipo de Profissional: $redstar")], [$tipoprofissional_id]);
        $this->form->addFields([new TLabel("Numero do Conselho: $redstar")], [$numeroconselho]);
        $this->form->addFields([new TLabel("CPF: $redstar")], [$numerocpf]);
        $this->form->addFields([new TLabel("Endereço: $redstar")], [$endereco]);
        $this->form->addFields([new TLabel("Bairro: $redstar")], [$bairro]);
        $this->form->addFields([new TLabel("Cidade: $redstar")], [$cidade]);
        $this->form->addFields([new TLabel("UF: $redstar")], [$uf]);
        $this->form->addFields([new TLabel("Telefone: $redstar")], [$telefone1]);
        $this->form->addFields([new TLabel("Telefone: $redstar")], [$telefone2]);
        $this->form->addFields([new TLabel("E-mail: $redstar")], [$email]);
        $this->form->addFields([new TLabel("Situação: $redstar")], [$situacao]);
        $this->form->addFields( [ $id ] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );
        $this->form->addAction( "Voltar", new TAction( [ "ProfissionalList", "onReload" ] ), "fa:table blue" );

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

                $object = $this->form->getData("ProfissionalRecord");
                $object->store();

            TTransaction::close();

            $action = new TAction( [ "ProfissionalList", "onReload" ] );

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

                $object = new ProfissionalRecord($param["key"]);

                $this->form->setData($object);

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}
