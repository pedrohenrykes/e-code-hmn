<?php

//use FDPF;
use Adianti\Database\TTransaction;
use Adianti\Database\TRepository;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;

class RelatorioBauAtendidoPDF extends FPDF {

    function Header() {

        $this->Image("app/images/system/hmn.jpg", 8, 15, 28, 10);

        $this->SetFont('Arial', 'B', 12);
        $this->SetY("15");
        $this->SetX("35");
        $this->Cell(0, 5, utf8_decode("HOSPITAL MUNICIPAL DE NATAL"), 0, 1, 'L');
        $this->SetX("35");
        $this->Cell(0, 5, utf8_decode("RELATÓRIO DE PACIENTES POR DATA"), 0, 1, 'L');
        $this->SetX("35");
        $this->Cell(0, 5, utf8_decode("SITUAÇÃO"), 0, 1, 'L');


        $this->Ln(6);

        $this->ColumnHeader();
    }

    function ColumnHeader() {

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(80 , 5, utf8_decode("PACIENTE"), 1, 0, 'L');
        $this->Cell(35 , 5, utf8_decode("ENTRADA"), 1, 0, 'L');
        $this->Cell(35 , 5, utf8_decode("SITUAÇÃO"), 1, 0, 'L');
        $this->Cell(41 , 5, utf8_decode("CLASSIFICAÇÃO"), 1, 1, 'L');

    }

    function ColumnDetail() {

        $situacao =  $_REQUEST['situacao'];
        $total = 0;

        TTransaction::open('database');
        $repository = new TRepository('VwBauPacientesRecord');

        $criteria = new TCriteria;

        $criteria->setProperty('order', 'dataentrada', 'asc');
        if ($situacao != 'TODOS'){
            $criteria->add(new TFilter('situacao', '=', $situacao ));
        }

        $rows = $repository->load($criteria);

        if ($rows) {

            foreach ($rows as $row) {

                $this->SetFont('Arial', '', 8);
                $this->Cell(80, 5, utf8_decode($row->nomepaciente), 0, 0, 'L');
                $this->Cell(35, 5, utf8_decode($row->dataentrada), 0, 0, 'L');
                $this->Cell(35, 5, utf8_decode($row->situacao), 0, 0, 'L');
                $this->Cell(40, 5, utf8_decode($row->nometipoclassificacaorisco), 0, 1, 'L');
                $this->Cell(0, 0, '', 1, 1, 'L');

                $total++;
            }

            $this->SetFont('Arial', 'B', 10);
            $this->Cell(40, 5, utf8_decode('Total:  ' .$total . ' Pacientes'), 0, 1, 'L');


        }

        TTransaction::close();
    }

    function Footer() {

        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);

        $data = date("d/m/Y H:i:s");
        $conteudo = "< E-CODE > Software House - impresso em " . $data;
        $texto = "http://kironsaude.educacao.ws";

        $this->Cell(0, 0, '', 1, 1, 'L');

        $this->Cell(0, 5, $texto, 0, 0, 'L');
        $this->Cell(0, 5, $conteudo, 0, 0, 'R');
        $this->Ln();
    }
}


$pdf = new RelatorioBauAtendidoPDF("P", "mm", "A4");
$pdf->SetTitle(utf8_decode("RELATÓRIO DE PACIENTES POR DATA - HMN"));
$pdf->SetSubject(utf8_decode("RELATÓRIO DE PACIENTES POR DATA - HMN"));


$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times', '', 12);
$pdf->ColumnDetail();
//$file = "app/reports/RelatorioMedMaisUtilizadoPDF".$_SESSION['medico_id'].".pdf";
$file = "app/reports/RelatorioBauAtendidoPDF.pdf";

//abrir pdf
$pdf->Output($file);
$pdf->openFile($file);


?>
