<?php
	// enable sessions
 //   session_start();
	
 //   $dom = simplexml_load_file("vote-nums.xml");
	
	if (isset($_GET["option"]) )
    {
		$fp = fopen("lock-vote.txt", "w");
		if (flock($fp, LOCK_EX| LOCK_NB)) {
			//echo "Got lock!\n";
			//sleep(10);
			$dom = new DOMDocument();  
			$dom->load("vote-nums.xml");
			$dom->formatOutput = true;	

			$xpath = new DOMXpath($dom);
			$results=$xpath->query("/items/item[@id='".$_GET["option"]."']");
			$num = $results->item(0)->nodeValue;
			$results->item(0)->nodeValue= $num + 1;
			$dom->save("vote-nums.xml");
			flock($fp, LOCK_UN);
		}else{ // Cannot get the lock, show "vote again!!":
			//echo "Cannot get lock!\n";
		}
		$host = $_SERVER["HTTP_HOST"];
		header("Location:http://".$host.$_SERVER["PHP_SELF"]);
		exit;
	}else{ // show the page:
		$dom = new DOMDocument();  
		$dom->load("vote-nums.xml");
		$dom->formatOutput = true;	
		
	}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="mystyle.css" />
<title>你现在持仓多少？(投票)</title>
</head>
 <body>

	<div align="center"><img src="[f160].gif" /></div>
	<div  align="center">
	<h4>你现在持仓多少？(投票)</h4>
	    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="get"> 
		<table id="main" >
		
		<?php
        $items=$dom->getElementsByTagName('item');
		foreach($items as $item){ ?>
			
			<tr >
			<td style="height:25px;">
				<input type="radio" name="option" value="<?php echo $item->getAttribute("id"); ?>"  />
				<?php			
					echo $item->getAttribute("name");
				?>
			</td>
			</tr>
			<tr>
				<td style="width:550px">
					<div id=0 style="background-color:#ebebeb; width:<?php echo 3*$item->nodeValue;?>px; height:25px; border-width:0px;">
					</div>
				</td>
				<td>
					<label id="label0">
					<?php 				        
						echo $item->nodeValue;
					?>
					</label>票
				</td>
			</tr>
		<?php }
		?>
		
			<tr>
				<td><p>
					<input type="submit" value="确认投票" onclick="return vote();" class= "buttonClass "/>		
				</td>
			</tr>
		</table>
	</div>
</body>
<script type="text/javascript">
	var shouldvote = false;
	function vote(){
		for(var i = 0; i < document.getElementsByName("option").length; i++){
			if(document.getElementsByName("option")[i].checked == true){				
				shouldvote = true;
			}
		}
		if(shouldvote == false){
			alert("请选择一项！");
			return false;
		}else{
			return true;
		}
	}

</script>
</html>