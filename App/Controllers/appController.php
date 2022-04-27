<?php
 	namespace App\Controllers;
	use MF\Controller\Action;
	use MF\Model\Container;
	
	class appController extends Action{
	 	public function timeline(){
	 		$this->validaAutenticacao();
	 			$tweet =Container::getModel('tweet');
	 			$tweet->__set('id_usuario', $_SESSION['id']);
	 		$totalRegistrosPagina = 10;
	 		$deslocamento = 0;
	 		$pagina =isset($_GET['pagina']) ? $_GET['pagina'] :1;
	 		$deslocamento=($pagina -1)*$totalRegistrosPagina;
	 		$tweets = $tweet->getPorPagina($totalRegistrosPagina, $deslocamento);
	 		$totalTweets=$tweet->getTotalRegistros();
			$this->view->totalPaginas=ceil($totalTweets['total']/$totalRegistrosPagina);
			$this->view->paginaAtiva=$pagina;
	 		$this->view->tweets=$tweets;
	 			$usuario=Container::getModel('usuario');
	 			$usuario->__set('id', $_SESSION['id']);
	 			$this->view->infoUsuario=$usuario->getInfoUsuario();
	 			$this->view->totalTweets=$usuario->getTotalTweets();
	 			$this->view->totalSeguindo=$usuario->getTotalSeguindo();
	 			$this->view->totalSeguidores=$usuario->getTotalSeguidores();
	 		$this->render('timeline');
		}
		public function tweet(){
			$this->validaAutenticacao();
				$tweet = container::getModel('tweet');
				$tweet-> __set('tweet',$_POST['tweet']);
				$tweet-> __set('id_usuario',$_SESSION['id']);
				$tweet->salvar();
				header("Location: /timeline");
		}
		public function validaAutenticacao(){
			session_start();
			if(!isset($_SESSION['id'])||$_SESSION['id'] =='' || !isset($_SESSION['nome'])||$_SESSION['nome'] ==''){
				header("Location: /?login=erro");
			}
		}
		public function quemSeguir(){
			$this->validaAutenticacao();
			
			$pesquisarPor =isset($_GET['pesquisarPor'])? $_GET['pesquisarPor']: '';
			$usuarios= array();

			if($pesquisarPor != '' ){
				$usuario = Container::getModel('usuario');
				$usuario->__set('nome', $pesquisarPor);
				$usuario->__set('id', $_SESSION['id']);
				$usuarios=$usuario->getAll();
			}else{
				$usuario = container::getModel('usuario');
			}
			$this->view->usuarios =$usuarios;
				$usuario=Container::getModel('usuario');
	 			$usuario->__set('id', $_SESSION['id']);
	 			$this->view->infoUsuario=$usuario->getInfoUsuario();
	 			$this->view->totalTweets=$usuario->getTotalTweets();
	 			$this->view->totalSeguindo=$usuario->getTotalSeguindo();
	 			$this->view->totalSeguidores=$usuario->getTotalSeguidores();
			$this->render('quemSeguir');
		}
		public function acao(){
			$this->validaAutenticacao();
			$acao = isset($_GET['acao']) ? $_GET['acao'] :'';
			$idUsuarioSeguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] :'';
			$usuario = Container::getModel("usuario");
			$usuario->__set('id', $_SESSION['id']);
			if($acao== 'seguir'){
				$usuario->seguirUsuario($idUsuarioSeguindo);
			}else if($acao == 'deixarDeSeguir'){
				$usuario->deixarSeguirUsuario($idUsuarioSeguindo);
			}
			header('location:/quemSeguir');
		}
		public function rtweet(){
			$this->validaAutenticacao();
			$rtweet = isset($_GET['rtweet']) ? $_GET['rtweet'] :'';
			$idtweet = isset($_GET['idtweet']) ? $_GET['idtweet'] :'';
			$usuario = Container::getModel("tweet");
			$usuario->__set('id', $_SESSION['id']);
			if($rtweet== 'deleta'){
				$usuario->deleta($idtweet);
			}
			header('location:/timeline');
		}
	}
?>