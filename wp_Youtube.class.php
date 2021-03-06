<?php

	class youtube_Videos{

		private $key;

		public function __construct( $key ){
			$this->key = "{API_KEY_HERE}";
		}

		private function fetch_by_tag( $keyword ){

			$keyword = urldecode( $keyword );

			$url 	= "https://www.googleapis.com/youtube/v3/search?part=snippet&q=".$keyword."&maxResults=21&safesearch=strict&key=".$this->key;

			$data   = wp_remote_get( $url );

			try{

				if ( is_wp_error( $data ) ) {
					return;
				}else{
					$response = wp_remote_retrieve_body( $data );

					if( is_wp_error( $response ) ){
						return; 
					}
				}

			}catch (Exception $ex){

				$response = null;

			}

			return $response;

		}

		private function fetch_by_playlist( $playlistId ){

			$playlist = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=50&playlistId=".$playlistId."&key=".$this->key;
			$data   = wp_remote_get( $playlist );

			try{

				if ( is_wp_error( $data ) ) {
					echo "erreur";

				}else{

					$response = wp_remote_retrieve_body( $data );
					
					if( is_wp_error( $response ) ){
						return; 
					}
				}

			}catch (Exception $ex){

				$response = null;

			}
			return $response;
		}
keyword
		public function fetch_youtube( $mode, $keyword = null , $playlistId = null ){

			if( $mode == "playlist"){
				$display = $this->fetch_by_playlist( $playlistId );
				
			}else{
				$display = $this->fetch_by_tag( $keyword );
			}
			
			$display = json_decode($display);

			foreach( $display->items as $i){

				if($mode == "playlist"){
					$videoId = $i->snippet->resourceId->videoId;
				}else{
					$videoId = $i->id->videoId;
				}

				$output[] = array(
					'titre' => $i->snippet->title,
					'desc'  => $i->snippet->description,
					'thumb' => $i->snippet->thumbnails->medium,
					'id'    => $videoId,
				);
			}
			return $output;
		}
	}

	/*-----------------------------------
	HOW TO USE
	-------------------------------------*/
	# Initialize the class
	$youtube  = new youtube_Videos( );

	# Fetch playlist
	$playlistId = "{ YOUR_PLAYLIST_ID_HERE }";
	$playlist 	= $youtube->fetch_youtube( "playlist" , "", $playlistId );

	# Fetch videos
	# use comma instead of space for keyword (eg: don,joe instead of don joe)
	# pipe separates two keywords (eg : travel|computer )
	$keyword 	= "{ YOUR_KEYWORD_HERE }";
	$videos $youtube->fetch_youtube( "videos" , $keyword, $playlistId );
