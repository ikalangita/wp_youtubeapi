<?php

	class youtube_Videos{

		private $key;

		public function __construct( $key ){
			$this->key = "{API_KEY_HERE}";
		}

		private function fetch_by_tag( $motcle ){

			$motcle = urldecode( $motcle );

			$url 	= "https://www.googleapis.com/youtube/v3/search?part=snippet&q=".$motcle."&maxResults=21&safesearch=strict&key=".$this->key;

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

		public function fetch_youtube( $mode, $motcle = null , $playlistId = null ){

			if( $mode == "playlist"){
				$display = $this->fetch_by_playlist( $playlistId );
				
			}else{
				$display = $this->fetch_by_tag( $motcle );
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