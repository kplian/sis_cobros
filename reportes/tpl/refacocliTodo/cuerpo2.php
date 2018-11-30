<font size="8">
<?php 	?>
		<table width="100%" cellpadding="5px"  rules="cols" border="1">
	<tbody>	
		
		<tr> 
			
			<td width="5%" ><b>Nro:</b> 
				
			</td>
			<td width="35%" ><b>Razon Social:</b> 
				
			</td>
			<td width="10%" ><b>Cantidad Facturas:</b> 
				
			</td>
			<td width="20%" ><b>Total Importe de Facturas:</b>
				
			</td>
			
				
		   <td width="15%"><b>Total Importe Cobrado(BS):</b>
		   	
		   </td>
		    <td width="15%"><b>Total Saldo por Cobrar (BS):</b>
		   	
		   </td>	
			
		</tr>
		<?php 
		$i=0;
		$u=1;	
		$primeroS='';
		$conteoFactura=0;
		$totalImporteDOC=0;
		$totalImporteCobrado=0;
		$totalImporteSaldo=0;
		while ($this->factura[$i]['id']!=null) {
				
			if($primeroS != trim($this->factura[$i]['razon_social'])) {
				
			 ?>
		<tr> 
			<td  ><b></b><?php  echo $u; ?></td>		
			<td  ><b></b><?php  echo trim($this->factura[$i]['razon_social']); ?></td>
			<td  ><b></b><?php
				$w=0;
				while ($this->factura[$w]['id']!=null) {
					if($this->factura[$i]['razon_social'] == $this->factura[$w]['razon_social']){
						$conteoFactura++;
					}			
					$w++;	
				}
				echo $conteoFactura;
				$conteoFactura=0;
				
				
				?>
			</td>
			<td  ><b></b>
			<?php
				$w=0;
				$importeDocTotal=0;
				while ($this->factura[$w]['id']!=null) {
					if($this->factura[$i]['razon_social'] == $this->factura[$w]['razon_social']){
						$importeDocTotal=$importeDocTotal+$this->factura[$w]['importe_doc'];
					}			
					$w++;	
				}
				$totalImporteDOC=$totalImporteDOC + $importeDocTotal;
				echo number_format($importeDocTotal,2,".", ",");
				$importeDocTotal=0;
				
				
				?>	
			 </td>
			<td align="right" ><b></b>
				<?php
				$w=0;
				$importeCobradoTotal=0;
				while ($this->factura[$w]['id']!=null) {
					if($this->factura[$i]['razon_social'] == $this->factura[$w]['razon_social']){
						$importeCobradoTotal=$importeCobradoTotal+$this->factura[$w]['importe_cobrado_mb'];
					}			
					$w++;	
				}
				
				$totalImporteCobrado =$totalImporteCobrado + $importeCobradoTotal;
				echo number_format($importeCobradoTotal,2,".", ",");
				$importeCobradoTotal=0;
				
				
				?>	
			</td>
			<td  align="right"><b></b>
					<?php
				$w=0;
				$importeSaldoTotal=0;
				while ($this->factura[$w]['id']!=null) {
					if($this->factura[$i]['razon_social'] == $this->factura[$w]['razon_social']){
						$importeSaldoTotal=$importeSaldoTotal+$this->factura[$w]['saldo_por_cobrar'];
					}			
					$w++;	
				}
				$totalImporteSaldo =$totalImporteSaldo + $importeSaldoTotal;
				echo number_format($importeSaldoTotal,2,".", ",");
				$importeSaldoTotal=0;
				
				
				?>	
			</td>
		</tr>
			
		<?php 
			$primeroS =$this->factura[$i]['razon_social'];
			$u++;
			}
				
		
	    $i++;  
		
			}?>
		
	
		<tr> 
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp; </td>
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp; </td>
			<td  ><b></b>&nbsp;&nbsp;&nbsp;&nbsp; </td>
			<td  ><b>Total Facturado: </b><?php echo number_format($totalImporteDOC,2,".", ","); ?> </td>
			<td  align="" ><b>Total Cobrado:   </b><?php echo number_format($totalImporteCobrado,2,".", ","); ?> </td>
			<td  align="" ><b>Saldo por Cobrar:</b> <?php echo number_format($totalImporteSaldo,2,".", ","); ?></td>
		</tr>
	
		
	 
	
	</tbody>
	</table>
	
