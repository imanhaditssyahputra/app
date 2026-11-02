<?php

namespace App\Helpers;
error_reporting(0);
use Fpdf;

class simkeu_faktur_helper extends FPDF
{
    
    public function __construct()
    {
        parent::__construct();
    }

    var $widths;
    var $aligns;
    
      protected $javascript;
      protected $n_js;

      protected $orientasi;
      protected $ukuran;
      protected $judul;
      protected $subjudul;
      protected $namaunit;
      protected $nama;
      protected $alamat1;
      protected $alamat2;
      protected $hp;
      protected $email;
  
      function IncludeJS($script, $isUTF8=false) {
        //   if(!$isUTF8)
        //       $script=utf8_encode($script);
          $this->javascript=$script;
      }
  
      function _putjavascript() {
          $this->_newobj();
          $this->n_js=$this->n;
          $this->_put('<<');
          $this->_put('/Names [(EmbeddedJS) '.($this->n+1).' 0 R]');
          $this->_put('>>');
          $this->_put('endobj');
          $this->_newobj();
          $this->_put('<<');
          $this->_put('/S /JavaScript');
          $this->_put('/JS '.$this->_textstring($this->javascript));
          $this->_put('>>');
          $this->_put('endobj');
      }
  
      function _putresources() {
          parent::_putresources();
          if (!empty($this->javascript)) {
              $this->_putjavascript();
          }
      }
  
      function _putcatalog() {
          parent::_putcatalog();
          if (!empty($this->javascript)) {
              $this->_put('/Names <</JavaScript '.($this->n_js).' 0 R>>');
          }
      }
      
  function AutoPrint($printer='')
      {
          // Open the print dialog
          if($printer)
          {
              $printer = str_replace('\\', '\\\\', $printer);
              $script = "var pp = getPrintParams();";
              $script .= "pp.interactive = pp.constants.interactionLevel.full;";
              $script .= "pp.printerName = '$printer'";
              $script .= "print(pp);";
          }
          else
              $script = 'print(true);';
          $this->IncludeJS($script);
      }
      
  function SetWidths($w)
  {
    $this->widths=$w;
  }
  
  function SetAligns($a)
  {
    $this->aligns=$a;
  }
  
  function CheckPageBreak($h)
  {   
      if($this->GetY()+$h>$this->PageBreakTrigger)
      //$this->AddPage($this->CurOrientation);
      $this->AddPage($this->orientasi,$this->ukuran);
  }
  
  function NbLines($w,$txt)
  {
      //Computes the number of lines a MultiCell of width w will take
      $cw=&$this->CurrentFont['cw'];
      if($w==0)
          $w=$this->w-$this->rMargin-$this->x;
      $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
      $s=str_replace("\r",'',$txt);
      $nb=strlen($s);
      if($nb>0 and $s[$nb-1]=="\n")
          $nb--;
      $sep=-1;
      $i=0;
      $j=0;
      $l=0;
      $nl=1;
      while($i<$nb)
      {
          $c=$s[$i];
          if($c=="\n")
          {
              $i++;
              $sep=-1;
              $j=$i;
              $l=0;
              $nl++;
              continue;
          }
          if($c==' ')
              $sep=$i;
          $l+=$cw[$c];
          if($l>$wmax)
          {
              if($sep==-1)
              {
                  if($i==$j)
                      $i++;
              }
              else
                  $i=$sep+1;
              $sep=-1;
              $j=$i;
              $l=0;
              $nl++;
          }
          else
              $i++;
      }
      return $nl;
  }
  
  function Row($data)
  {
      //Calculate the height of the row
      $nb=0;
      for($i=0;$i<count($data);$i++)
          $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
      $h=5*$nb;
      //Issue a page break first if needed
      $this->CheckPageBreak($h);
      //Draw the cells of the row
      for($i=0;$i<count($data);$i++)
      {
          $w=$this->widths[$i];
          $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
          //Save the current position
          $x=$this->GetX();
          $y=$this->GetY();
          //Draw the border
          $this->Rect($x,$y,$w,$h);
          //Print the text
          $this->MultiCell($w,5,$data[$i],0,$a);
          //Put the position to the right of the cell
          $this->SetXY($x+$w,$y);
      }
      //Go to the next line
      $this->Ln($h);
  }
  
  function RowB($data)
  {
      //Calculate the height of the row
      $nb=0;
      for($i=0;$i<count($data);$i++)
          $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
      $h=5*$nb;
      //Issue a page break first if needed
      $this->CheckPageBreak($h);
      //Draw the cells of the row
      for($i=0;$i<count($data);$i++)
      {
          $w=$this->widths[$i];
          $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
          //Save the current position
          $x=$this->GetX();
          $y=$this->GetY();
          //Draw the border
          
          //Print the text
          $this->MultiCell($w,5,$data[$i],0,$a);
          //Put the position to the right of the cell
          $this->SetXY($x+$w,$y);
      }
      //Go to the next line
      $this->Ln($h);
  }
  
  function FancyRow($data, $fc=array(), $border=array(), $align=array(), $style=array(), $size=array(), $maxline=array())
    {
          //Calculate the height of the row
          $nb = 0;
          for($i=0;$i<count($data);$i++) {
              $nb = max($nb, $this->NbLines($this->widths[$i],$data[$i]));
          }
          if (count($maxline)) {
              $_maxline = max($maxline);
              if ($nb > $_maxline) {
                  $nb = $_maxline;
              }
          }
          $h = 5*$nb;
          //Issue a page break first if needed
          $this->CheckPageBreak($h);
          //Draw the cells of the row
          for($i=0;$i<count($data);$i++) {
              $w=$this->widths[$i];
              // alignment
              $a = isset($align[$i]) ? $align[$i] : 'L';
              // maxline
              $m = isset($maxline[$i]) ? $maxline[$i] : false;
              //Save the current position
              $x = $this->GetX();
              $y = $this->GetY();
              //Draw the border
              if ($border[$i]==1) {
                  $this->Rect($x,$y,$w,$h);
              } else {
                  $_border = strtoupper($border[$i]);
                  if (strstr($_border, 'L')!==false) {
                      $this->Line($x, $y, $x, $y+$h);
                  }
                  if (strstr($_border, 'R')!==false) {
                      $this->Line($x+$w, $y, $x+$w, $y+$h);
                  }
                  if (strstr($_border, 'T')!==false) {
                      $this->Line($x, $y, $x+$w, $y);
                  }
                  if (strstr($_border, 'B')!==false) {
                      $this->Line($x, $y+$h, $x+$w, $y+$h);
                  }
              }
              // Setting Style
              if (isset($style[$i])) {
                  $this->SetFont('', $style[$i], $size[$i]);
              }
              //$this->SetFillColor(230,230,230);
              $this->MultiCell($w, 5, $data[$i], 0, $a, $fc[$i], $m);
              //Put the position to the right of the cell
              $this->SetXY($x+$w, $y);
          }
          //Go to the next line
          $this->Ln($h);
    }
  
      
    function FancyRowF($data, $border=array(), $align=array(), $style=array(), $size=array(), $maxline=array())
      {
          //Calculate the height of the row
          $nb = 0;
          for($i=0;$i<count($data);$i++) {
              $nb = max($nb, $this->NbLines($this->widths[$i],$data[$i]));
          }
          if (count($maxline)) {
              $_maxline = max($maxline);
              if ($nb > $_maxline) {
                  $nb = $_maxline;
              }
          }
          $h = 5*$nb;
          //Issue a page break first if needed
          $this->CheckPageBreak($h);
          //Draw the cells of the row
          for($i=0;$i<count($data);$i++) {
              $w=$this->widths[$i];
              // alignment
              $a = isset($align[$i]) ? $align[$i] : 'L';
              // maxline
              $m = isset($maxline[$i]) ? $maxline[$i] : false;
              //Save the current position
              $x = $this->GetX();
              $y = $this->GetY();
              //Draw the border
              if ($border[$i]==1) {
                  $this->Rect($x,$y,$w,$h);
              } else {
                  $_border = strtoupper($border[$i]);
                  if (strstr($_border, 'L')!==false) {
                      $this->Line($x, $y, $x, $y+$h);
                  }
                  if (strstr($_border, 'R')!==false) {
                      $this->Line($x+$w, $y, $x+$w, $y+$h);
                  }
                  if (strstr($_border, 'T')!==false) {
                      $this->Line($x, $y, $x+$w, $y);
                  }
                  if (strstr($_border, 'B')!==false) {
                      $this->Line($x, $y+$h, $x+$w, $y+$h);
                  }
              }
              // Setting Style
              if (isset($style[$i])) {
                  $this->SetFont('', $style[$i], $size[$i]);
              }
              $this->MultiCell($w, 5, $data[$i], 0, $a, 0, $m, true);
              //Put the position to the right of the cell
              $this->SetXY($x+$w, $y);
          }
          //Go to the next line
          $this->Ln($h);
      }
      
  function FancyRow2($h, $data, $fc, $border=array(), $align=array(), $style=array(), $size=array(), $maxline=array())

      {
          //Calculate the height of the row
          $nb = 0;
          for($i=0;$i<count($data);$i++) {
              $nb = max($nb, $this->NbLines($this->widths[$i],$data[$i]));
          }
          if (count($maxline)) {
              $_maxline = max($maxline);
              if ($nb > $_maxline) {
                  $nb = $_maxline;
              }
          }
          //$h = 5*$nb;
          //Issue a page break first if needed
          $this->CheckPageBreak($h);
          //Draw the cells of the row
          for($i=0;$i<count($data);$i++) {
              $w=$this->widths[$i];
              // alignment
              $a = isset($align[$i]) ? $align[$i] : 'L';
              // maxline
              $m = isset($maxline[$i]) ? $maxline[$i] : false;
              //Save the current position
              $x = $this->GetX();
              $y = $this->GetY();
              //Draw the border
              if ($border[$i]==1) {
                  $this->Rect($x,$y,$w,$h);
              } else {
                  $_border = strtoupper($border[$i]);
                  if (strstr($_border, 'L')!==false) {
                      $this->Line($x, $y, $x, $y+$h);
                  }
                  if (strstr($_border, 'R')!==false) {
                      $this->Line($x+$w, $y, $x+$w, $y+$h);
                  }
                  if (strstr($_border, 'T')!==false) {
                      $this->Line($x, $y, $x+$w, $y);
                  }
                  if (strstr($_border, 'B')!==false) {
                      $this->Line($x, $y+$h, $x+$w, $y+$h);
                  }
              }
              // Setting Style
              if (isset($style[$i])) {
                  $this->SetFont('', $style[$i], $size[$i]);
              }
              $this->MultiCell($w, $h, $data[$i], 0, $a, $fc[$i], $m);
              //Put the position to the right of the cell
              $this->SetXY($x+$w, $y);
          }
          //Go to the next line
          $this->Ln($h);
    }   
  
  function Header()
  {   
     if($this->nama != ''){
        $this->image('./assets/images/logo_PT.jpeg',10,10,25,20);
        $this->cell(25);
        $this->SetFont('Arial','B',20);
        $this->cell(0,10,$this->nama,0,1); 
        $this->cell(25);
        $this->SetFont('Arial','',9);
        $this->cell(102,0,$this->alamat1,$this->hp,0,1); 
        $this->cell(9,0,'Telp :',$this->hp,0,1);
        $this->cell(23,0,$this->hp,$this->email,0,1);     
        $this->cell(0,0,$this->email,0,1);  
        $this->cell(25);
        $this->SetFont('Arial','',9);
        $this->cell(0,7,'NPWP : 31.452.155.0-445.000 IDAK/SDAK : 81201018710390001',0,0);         
    }   
        $this->ln(5.5);
        $this->SetTextColor(0);  
        $this->SetFont('Times','B',12);  
        $this->cell(0,0,$this->judul,0,1,'C');   
        if($this->subjudul !=""){
            $this->SetFont('Times','',10);  
            $this->cell(0,5,$this->subjudul,0,1,'C');   
        }
        $this->ln(12);
    
    
  }
  
  function Footer()
  { 
      $this->SetY(-15);
      $this->SetFont('Times','I',8);
      $this->Cell(0,10,'Halaman  '.$this->PageNo().' dari {nb}',0,0,'L');
      $this->Cell(0,10,'Tercetak '.date('d-m-Y H:i:s'),0,0,'R');
  }
  
  
  public function setID($n,$m,$a,$hp,$email)
  {
    $this->nama=$n;
    $this->alamat1=$m;
    $this->alamat2=$a;
    $this->hp=$hp;
    $this->email=$email;
  }
  
  public function setjudul($jd)
  {
    $this->judul=$jd;
  }
  
  public function setsubjudul($jd)
  {
    $this->subjudul=$jd;
  }
  
  public function setunit($nama)
  {
    $this->namaunit=$nama;
  }
  
  
  public function setsize($ori,$sz)
  {
    $this->orientasi=$ori;
    $this->ukuran=$sz;
  }
  
  
  
}
  
?>