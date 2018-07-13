<html>
<body>
	<script type="text/javascript">
	function hello(){
	<?
	echo("var hv_var='Hello world!';\n");
	?>
	document.getElementById('hw').value=hv_var;
	}
	</script>
	<p id="hw">blah</p>
	<script type="text/javascript">
		hello();
	</script>
</body>
</html>