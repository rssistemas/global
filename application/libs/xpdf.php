<?php
	require_once "fpdf.php";
	class xpdf extends fpdf
	{
	
        function Header() {

        date_default_timezone_set('America/Caracas');
       
        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(75,75,75);
        $this->SetY(2);
        $FechaActual = date("d/m/Y").' - '.date('h:i a', time() - 3600 * date('I'));
        
        $this->Cell(0, 4, $FechaActual, 0, 1, "R"); 
        //$this->Cell(0, 5,'GlobalAdm', 0, 1, 'R');
        $this->Cell(0,5, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
        $this->SetXY($this->Getx()+5,$this->Gety()+5);
        
    }

        function Footer() {
        
       
        $this->SetY(-5);
        $this->SetFont('Arial', 'I', 8);
		//Dirección de Hacienda; Avenida 03, entre calle 12 y 13,
		// Centro Plaza Edificio Municipio Agua Blanca – Estado Portuguesa
		$this->Cell(180,5, utf8_decode('Documento generado mediante sistema en linea GlobalAdm. Para mayor informacion por los Teléfonos. 0416-1971855'),0,1,'L');      
       
        
        
    }	
	
	
    //***** Aquí comienza código para ajustar texto *************
    //***********************************************************
    public function CellFit($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $scale=false, $force=true)
    {
        //Get string width
        $str_width=$this->GetStringWidth($txt);
 
        //Calculate ratio to fit cell
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
        $ratio = ($w-$this->cMargin*2)/$str_width;
 
        $fit = ($ratio < 1 || ($ratio > 1 && $force));
        if ($fit)
        {
            if ($scale)
            {
                //Calculate horizontal scaling
                $horiz_scale=$ratio*100.0;
                //Set horizontal scaling
                $this->_out(sprintf('BT %.2F Tz ET',$horiz_scale));
            }
            else
            {
                //Calculate character spacing in points
                $char_space=($w-$this->cMargin*2-$str_width)/max($this->MBGetStringLength($txt)-1,1)*$this->k;
                //Set character spacing
                $this->_out(sprintf('BT %.2F Tc ET',$char_space));
            }
            //Override user alignment (since text will fill up cell)
            $align='';
        }
 
        //Pass on to Cell method
        $this->Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);
 
        //Reset character spacing/horizontal scaling
        if ($fit)
            $this->_out('BT '.($scale ? '100 Tz' : '0 Tc').' ET');
    }
 
    public function CellFitSpace($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        $this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,false,false);
    }
 
    //Patch to also work with CJK double-byte text
    public function MBGetStringLength($s)
    {
        if($this->CurrentFont['type']=='Type0')
        {
            $len = 0;
            $nbbytes = strlen($s);
            for ($i = 0; $i < $nbbytes; $i++)
            {
                if (ord($s[$i])<128)
                    $len++;
                else
                {
                    $len++;
                    $i++;
                }
            }
            return $len;
        }
        else
            return strlen($s);
    }
//************** Fin del código para ajustar texto *****************
//******************************************************************
    var $angle=0;
    public function Rotate($angle,$x=-1,$y=-1)
    {
            if($x==-1)
                    $x=$this->x;
            if($y==-1)
                    $y=$this->y;
            if($this->angle!=0)
                    $this->_out('Q');
            $this->angle=$angle;
            if($angle!=0)
            {
                    $angle*=M_PI/180;
                    $c=cos($angle);
                    $s=sin($angle);
                    $cx=$x*$this->k;
                    $cy=($this->h-$y)*$this->k;
                    $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
            }
    }

    public function _endpage()
    {
            if($this->angle!=0)
            {
                    $this->angle=0;
                    $this->_out('Q');
            }
            parent::_endpage();
    }
///-------------------------------------------------------------------------------------
//manipulacion del canal alfa para imagenes jpg
//-------------------------------------------------------------------------------------- 
//    public function SetAlpha($alpha, $bm='Normal')
//    {
//        // set alpha for stroking (CA) and non-stroking (ca) operations
//        $gs = $this->AddExtGState(array('ca'=>$alpha, 'CA'=>$alpha, 'BM'=>'/'.$bm));
//        $this->SetExtGState($gs);
//    }
//
//    public function AddExtGState($parms)
//    {
//        $n = count($this->extgstates)+1;
//        $this->extgstates[$n]['parms'] = $parms;
//        return $n;
//    }
//
//    public function SetExtGState($gs)
//    {
//        $this->_out(sprintf('/GS%d gs', $gs));
//    }
//
//    public function _enddoc()
//    {
//        if(!empty($this->extgstates) && $this->PDFVersion<'1.4')
//            $this->PDFVersion='1.4';
//        parent::_enddoc();
//    }
//
//    public function _putextgstates()
//    {
//        for ($i = 1; $i <= count($this->extgstates); $i++)
//        {
//            $this->_newobj();
//            $this->extgstates[$i]['n'] = $this->n;
//            $this->_out('<</Type /ExtGState');
//            foreach ($this->extgstates[$i]['parms'] as $k=>$v)
//                $this->_out('/'.$k.' '.$v);
//            $this->_out('>>');
//            $this->_out('endobj');
//        }
//    }
//
//    public function _putresourcedict()
//    {
//        parent::_putresourcedict();
//        $this->_out('/ExtGState <<');
//        foreach($this->extgstates as $k=>$extgstate)
//            $this->_out('/GS'.$k.' '.$extgstate['n'].' 0 R');
//        $this->_out('>>');
//    }
//
//    public function _putresources()
//    {
//        $this->_putextgstates();
//        parent::_putresources();
//    }
//------------------------------------------------------------------------------------------
//funciones para generar codigo de barras code 39
//------------------------------------------------------------------------------------------
    public function Code39($xpos, $ypos, $code, $baseline=0.5, $height=5)
        {

                $wide = $baseline;
                $narrow = $baseline / 3 ; 
                $gap = $narrow;

                $barChar['0'] = 'nnnwwnwnn';
                $barChar['1'] = 'wnnwnnnnw';
                $barChar['2'] = 'nnwwnnnnw';
                $barChar['3'] = 'wnwwnnnnn';
                $barChar['4'] = 'nnnwwnnnw';
                $barChar['5'] = 'wnnwwnnnn';
                $barChar['6'] = 'nnwwwnnnn';
                $barChar['7'] = 'nnnwnnwnw';
                $barChar['8'] = 'wnnwnnwnn';
                $barChar['9'] = 'nnwwnnwnn';
                $barChar['A'] = 'wnnnnwnnw';
                $barChar['B'] = 'nnwnnwnnw';
                $barChar['C'] = 'wnwnnwnnn';
                $barChar['D'] = 'nnnnwwnnw';
                $barChar['E'] = 'wnnnwwnnn';
                $barChar['F'] = 'nnwnwwnnn';
                $barChar['G'] = 'nnnnnwwnw';
                $barChar['H'] = 'wnnnnwwnn';
                $barChar['I'] = 'nnwnnwwnn';
                $barChar['J'] = 'nnnnwwwnn';
                $barChar['K'] = 'wnnnnnnww';
                $barChar['L'] = 'nnwnnnnww';
                $barChar['M'] = 'wnwnnnnwn';
                $barChar['N'] = 'nnnnwnnww';
                $barChar['O'] = 'wnnnwnnwn'; 
                $barChar['P'] = 'nnwnwnnwn';
                $barChar['Q'] = 'nnnnnnwww';
                $barChar['R'] = 'wnnnnnwwn';
                $barChar['S'] = 'nnwnnnwwn';
                $barChar['T'] = 'nnnnwnwwn';
                $barChar['U'] = 'wwnnnnnnw';
                $barChar['V'] = 'nwwnnnnnw';
                $barChar['W'] = 'wwwnnnnnn';
                $barChar['X'] = 'nwnnwnnnw';
                $barChar['Y'] = 'wwnnwnnnn';
                $barChar['Z'] = 'nwwnwnnnn';
                $barChar['-'] = 'nwnnnnwnw';
                $barChar['.'] = 'wwnnnnwnn';
                $barChar[' '] = 'nwwnnnwnn';
                $barChar['*'] = 'nwnnwnwnn';
                $barChar['$'] = 'nwnwnwnnn';
                $barChar['/'] = 'nwnwnnnwn';
                $barChar['+'] = 'nwnnnwnwn';
                $barChar['%'] = 'nnnwnwnwn';

                $this->SetFont('Arial','',10);
                $this->Text($xpos, $ypos + $height + 4, $code);
                $this->SetFillColor(0);

                $code = '*'.strtoupper($code).'*';
                for($i=0; $i<strlen($code); $i++){
                        $char = $code[$i];
                        if(!isset($barChar[$char])){
                                $this->Error('Invalid character in barcode: '.$char);
                        }
                        $seq = $barChar[$char];
                        for($bar=0; $bar<9; $bar++){
                                if($seq[$bar] == 'n'){
                                        $lineWidth = $narrow;
                                }else{
                                        $lineWidth = $wide;
                                }
                                if($bar % 2 == 0){
                                        $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
                                }
                                $xpos += $lineWidth;
                        }
                        $xpos += $gap;
                }
        }
        
    public function crear_pagina($tipo,$ori)
    {
        
        if($tipo =='medicarta')
            $this->_pdf = new xpdf($ori, 'mm', array(215,139));
        else {
            $this->_pdf = new xpdf($ori);
        }
    }        

 }





?>
