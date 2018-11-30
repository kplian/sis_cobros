<font size="8">
<?php 	?>
		<table width="100%" cellpadding="5px"  rules="cols" border="1">
	<tbody>	
		
		<tr> 
			
			<td width="5%" ><b>Nro</b> 
				
			</td>
			<td width="10%" ><b>Fecha Factura</b> 
				
			</td>
			<td width="8%" ><b>Nro Factura</b> 
				
			</td>
			<td width="15%" ><b>Importe de Factura</b>
				
			</td>
			
			<td width="7%"><b>Moneda</b>
		   	
		   </td>
			
			<td width="25%"><b>Cobros</b><br>(Nro Tramite Cobro-Cobro a Factura)
				 	 	
			 </td>
			
		   <td width="15%"><b>Importe Cobrado(BS)</b>
		   	
		   </td>
		    <td width="15%"><b>Saldo por Cobrar(BS)</b>
		   	
		   </td>	
			
		</tr>
		<?php 
		$i=0;
		$u=1;
		$saldo=$this->cabecera[$t]['importe_doc'];
		$primeroS=0;
		$saldoTotal=0;
		$importeCobradoTotal=0;
		while ($this->cabecera[$i]['id']!=null) {
				
			if($primeroS != $this->cabecera[$i]['id_doc_compra_venta']) {
			$SaldoPorCobrar= $this->cabecera[$i]['saldo_por_cobrar'];	
			$saldoTotal=$saldoTotal+$SaldoPorCobrar;
			$importeCobrado= $this->cabecera[$i]['importe_cobrado_mb'];	
			$importeTotalCobrado=$importeTotalCobrado + $importeCobrado;
			
			$importeDoc= $this->cabecera[$i]['importe_doc'];	
			$importeTotalDoc=$importeTotalDoc + $importeDoc;
		 ?>
		<tr> 
			<td  ><b></b><?php  echo $u; ?></td>		
			<td  ><b></b><?php  echo $this->cabecera[$i]['fecha']; ?></td>
			<td  ><b></b><?php  echo $this->cabecera[$i]['nro_documento']; ?> </td>
			<td  ><b></b> <?php  echo number_format($this->cabecera[$i]['importe_doc'],2,".", ","); ?></td>
			<td  ><b></b><?php  echo $this->cabecera[$i]['desc_moneda']; ?></td>
			
			<td  ><b></b> <?php 
			$t=0;
			while ($this->cabecera[$t]['id']!=null) {
				
				$primero=$this->cabecera[$t]['id_doc_compra_venta'];
				if ($primero == $this->cabecera[$i]['id_doc_compra_venta']) {
					 if ($this->cabecera[$t]['nro_tramite_cobro']!=null) {
					 echo $this->cabecera[$t]['nro_tramite_cobro'];
						echo '   -   ';
					 echo $this->cabecera[$t]['importe_cobro_factura'];
					 echo ' (Bs)';
					 }
				}
			$primero=$this->cabecera[$t]['id_doc_compra_venta'];
			$t++;	
			} ?>
			 <!--b>Total=</b-->
			 
			 <?php  
			//echo $this->cabecera[$i]['importe_cobrado_mb'];
			//echo ' (Bs)';
			?>
			</td>
			
			
			<td align="right" ><b></b><?php  echo number_format($this->cabecera[$i]['importe_cobrado_mb'],2,".", ","); ?> </td>
			<td  align="right"><b></b><?php  echo number_format($this->cabecera[$i]['saldo_por_cobrar'],2,".", ",");; ?> </td>
		</tr>
			
		<?php 
			$primeroS =$this->cabecera[$i]['id_doc_compra_venta'];
			$u++;
			}
		
	    $i++;  
		
			}?>
		
	
		<tr> 
			<td  ><b></b> </td>
			<td  ><b></b> </td>
			<td  ><b></b> </td>
			<td  align="" ><b>Total Importe:</b><?php echo number_format($importeTotalDoc,2,".", ","); ?> </td>
			<td  ><b></b> </td>
			<td  ><b></b> </td>
			<td  align="rigth" ><b>Total Cobrado:   </b><?php echo number_format($importeTotalCobrado,2,".", ","); ?> </td>
			<td  align="right" ><b>Saldo por Cobrar:</b> <?php  echo number_format($saldoTotal,2,".", ","); ?> </td>
		</tr>
	
		
	 
	
	</tbody>
	</table>
	
