<style>
	body{
		background-color: #EFEFEF;
		font-family: "Lucida Sans Unicode", "Lucida Grande", Verdana, Arial, Helvetica, sans-serif;
		font-size: 0.95em;
	}
		
	#GCsystem{
		width: 810px;
		height: 610px;
		background-color: white;
		border: 1px solid #DFDFDF;
		-moz-border-radius: 16px;
		-webkit-border-radius: 16px;
		border-radius: 16px;
		margin-bottom: 20px;
		word-wrap: break-word;
		position:absolute; 
		top:50%; 
		left:50%; 
		margin-left:-400px; 
		margin-top:-350px;
	}
	
	#GCsystem_left{
		float: left;
		width: 200px;
		height: 610px;
		background-color: rgb(230,230,230);
		border-top-left-radius: 16px;
		border-bottom-left-radius: 16px;
	}
	
	#GCsystem_right{
		padding: 5px;
		padding-left: 210px;
	}
	
	#GCsystem_right h1{
		font-size: 2em;
		color: #ff7800;
		text-align: center;
		margin: 0;
	}
	
	#GCsystem_right p{
		text-indent: 10px;
		text-align: justify;
	}
</style>
<div id="GCsystem">
	<div id="GCsystem_left">
		<img src="asset/image/GCsystem/logo.png" alt="logo"/>
		 {{gravatar:$var:500}} 
	</div>
	<div id="GCsystem_right">
		<h1>{{lang:bienvenue}}</h1>
		<p>{{lang:content}}</p>
		<ul>
			<li><a href="">{{lang:liredoc}}</a></li>
			<li><a href="">{{lang:lirecours}}</a></li>
			<li><a href="{{url:terminal:}}">terminal</a>
		</ul>
	</div>
 {<gc:function name="FilterTitle" string="SALUT5"/>}
	projet-{<gc:function name="FilterTitle" string="SALUT1"/>}.html -------- projet {<gc:function name="strtolower" string="SALUT1"/>} 
	{<gc:function name="strtolower" string="sdfFFFFFFFF"/>}
	<gc:variable mavar="1"/>
	<gc:variable mavara=45/>
	<gc:variable truc=strtolower('Machin')/>
	<br />{{url:index3:$truc,$sdfjkh}}
	{{php: 
	echo 'salusdfsdjfsdkjfht';}}
</div>

{fs} {qsd}

<gc:include file="test" />