<font size="8">
<table width="100%" cellpadding="5px"  rules="cols" border="1">
<tbody>	
		<tr>
			<td width="60%" ><b>Beneficiario:</b>&nbsp;&nbsp;&nbsp;&nbsp;  <?php  echo $this->cabecera[0]['desc_proveedor']; ?></td>
			
			  <td width="40%">
			  	<table width="100%" cellpadding="0px"  rules="cols" border="0">
			  	<tr><td width="100%"><b>Nro Trámite:</b>&nbsp;&nbsp;&nbsp;&nbsp;  <?php  echo $this->cabecera[0]['nro_tramite']; ?></td></tr>
			  	
			    </table>
			  </td>
			  
			
			
		</tr>
		
		<tr>
			<td><b>La Suma de :</b>&nbsp;&nbsp;&nbsp;&nbsp; <?php  echo trim($this->cabecera[0]['importe_literal']).'<BR/>'.trim($this->cabecera[0]['glosa2']); ?></td>
			<td><b>Total :</b>&nbsp;&nbsp;&nbsp;&nbsp; <?php  echo trim($this->cabecera[0]['importe']).' '.trim($this->cabecera[0]['codigo']); ?></td>
		</tr>
</tbody>
</table></font>