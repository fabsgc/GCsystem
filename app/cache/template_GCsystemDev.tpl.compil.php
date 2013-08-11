<style>
	body{
		margin-bottom: 60px;
	}
	
	#GCsysytem_dev{
		position: fixed;
		background-color: #F7F7F7;
		background-image: -moz-linear-gradient(-90deg, #E4E4E4, white);
		background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#E4E4E4), to(white));
		bottom: 0;
		left: 0;
		margin: 0;
		z-index: 6000000;
		width: 100%;
		border-top: 1px solid 
		#BBB;
		font: 16px Verdana, Arial, sans-serif;
		text-align: left;
		color: 
		#2F2F2F;
		min-height: 60px;
	}
	
	#GCsysytem_dev_logo{
		float: left;
		display: inline-block;
		width: 56px;
		height: 56px;
		border-right: 1px solid #CDCDCD;
		padding: 2px;
	}
	
	#GCsysytem_dev_logo img{
		width: 55px;
		height: 55px;
		float: left;
	}
	
	.GCsysytem_dev_content{
		float: left;
		display: block;
		height: 60px;
		font-size: 0.75em;
		border-right: 1px solid #CDCDCD;
	}
	
	.GCsysytem_dev_content img{
		float: left;
		padding: 4px;
		padding-top: 0px;
	}
	
	#GCsysytem_dev_content_zone{
		clear: left;
		background-color: white;
		width: 194px;
		height: 60px;
		word-wrap: breal-work;
		overflow: auto;
	}
	
	#GCsysytem_dev_content_zone::-webkit-scrollbar {
		width: 7px;
		height: 8px;
	}
	#GCsysytem_dev_content_zone::-webkit-scrollbar-track {
		background-color: rgba(113,112,107,0.1);
	}
	#GCsysytem_dev_content_zone::-webkit-scrollbar-thumb:vertical {
		background-color: rgba(0,0,0,.2);
	}
	#GCsysytem_dev_content_zone::-webkit-scrollbar-thumb:vertical:hover,
	#GCsysytem_dev_content_zone::-webkit-scrollbar-thumb:horizontal:hover {
		background: grey;
	}
	#GCsysytem_dev_content_zone::-webkit-scrollbar-thumb:horizontal {
		background-color: rgba(0,0,0,.2);
	}
	
	.GCsysytem_dev_content_logo{
		float: left;
		background-color: #CDCDCD;
		height: 60px;
		width: 30px;
	}
	
	.GCsysytem_dev_content_content{
		height: 60px;
		float: right;
		border-left: 1px solid #CDCDCD;
	}
	
</style>
<?php if($_GET['rubrique'] != 'terminal') { ?>
<?php } ?>
<div id="GCsysytem_dev">
	<div id="GCsysytem_dev_logo"><img src="<?php echo ($IMG_PATH); ?>logo.png"/></div>
	<div class="GCsysytem_dev_content">
		<div class="GCsysytem_dev_content_logo"><img src="<?php echo ($IMG_PATH); ?>time.png" title="<?php echo $this->useLang('executiontime',array()); ?>"/><img src="<?php echo ($IMG_PATH); ?>memory.png" title="<?php echo $this->useLang('memoireuse',array()); ?>"/></div>
		<div class="GCsysytem_dev_content_content" style="padding: 2px; width: 70px;"><?php echo ($timeexec); ?> ms<br /><span style="position: relative; top: 10px;"><?php echo ($memory); ?> kb</span></div>
	</div>
	<div class="GCsysytem_dev_content" style="width: 250px; padding: 0px;">
		<div class="GCsysytem_dev_content_logo"><img src="<?php echo ($IMG_PATH); ?>http.png" title="<?php echo $this->useLang('fichierinclus',array()); ?>"/></div>
		<div class="GCsysytem_dev_content_content">
			<div id="GCsysytem_dev_content_zone" style="width: 219px;">
			<?php echo ($http); ?>
			</div>
		</div>
	</div>
	<div class="GCsysytem_dev_content" style="width: 275px; padding: 0px;">
		<div class="GCsysytem_dev_content_logo"><img src="<?php echo ($IMG_PATH); ?>sql.png" title="<?php echo $this->useLang('requetesql',array()); ?>"/></div>
		<div class="GCsysytem_dev_content_content">
			<div id="GCsysytem_dev_content_zone" style="width: 244px;">
			<?php echo ($sql); ?>
			</div>
		</div>
	</div>
	<div class="GCsysytem_dev_content" style="width: 250px; padding: 0px;">
		<div class="GCsysytem_dev_content_logo"><img src="<?php echo ($IMG_PATH); ?>tpl.png" title="<?php echo $this->useLang('fichiertpl',array()); ?>"/></div>
		<div class="GCsysytem_dev_content_content">
			<div id="GCsysytem_dev_content_zone" style="width: 219px;">
			<?php echo ($tpl); ?>
			</div>
		</div>
	</div>
	<div class="GCsysytem_dev_content" style="width: 200px; padding: 0px;">
		<div class="GCsysytem_dev_content_logo"><img src="<?php echo ($IMG_PATH); ?>arbo.png" title="<?php echo $this->useLang('vargetpost',array()); ?>"/></div>
		<div class="GCsysytem_dev_content_content">
			<div id="GCsysytem_dev_content_zone"  style="width: 169px;">
				<?php echo (nl2br("$arbo")); ?>
			</div>
		</div>
	</div>
</div>