<?php

namespace crosenblum\movienforeader;

class movienforeader {
	
	// xmlreader based function for faster nfo or xml parsing
	public function moviereader(string $nfo_path) {

		// check if nfo file exists
		if (!file_exists($nfo_path)) {
		
			// return nothing
			return;
		
		}
		// setup variables and arrays
		$return = array();
		$actor_list = '';
		$actor_thumb_list = '';
		$genre_list = '';

		// get folder paths
		$fpath = pathinfo($nfo_path);
		$folder = $fpath['dirname'];
		$poster = $folder.'\\poster.jpg';

		// declare xml reader object
		$reader = new XMLReader();

		// open nfo file with xmlreader
		// read file external xml file...
		if (!$reader->open($nfo_path)) {
			die("Failed to open $nfo_path");
		}

		//Reading the contents
		$reader->read();

		// loop thru each node
		while ($reader->read()) {
			
			// check if this is an element node type
			if ($reader->nodeType == XMLReader::ELEMENT) {

				// change logic based on which element node it is
				switch($reader->name) {
					case 'title':
						// set to return array
						$return['title'] = $reader->readString();
						break;
					case 'year':
						// set to return array
						$return['year'] = $reader->readString();
						break;
					case 'runtime':
						// set to return array
						$return['runtime'] = $reader->readString();
						break;
					case 'thumb':
						// check if actual poster exists
						if (!file_exists($poster)) {
							// set to blank if does not exist
							$poster = '';
						}
						// set banner array to this url
						$return['banner_url'] = $reader->readString();
						break;
					case 'genre':
						// append to genre list
						$genre_list .= $reader->readString() . ', ';
						break;
					case 'rating':
						// choose imdb over themoviedb
						switch($reader->getAttribute('name')) {
							case 'imdb':
								$return['ratings'] = (float) $reader->readString('votes');
								break;
							case 'themoviedb':
								$return['ratings'] = (float) $reader->readString('votes');
								break;
							case 'tmdb':
								$return['ratings'] = (float) $reader->readString('votes');
								break;
							case 'default':
								$return['ratings'] = (float) $reader->readString('votes');
								break;
							default:
								$return['ratings'] = (float) $reader->readString('votes');
								break;
						}
						break;
					case 'plot':
						// set to return array
						$return['plot'] = $reader->readString();
						break;
					case 'tagline':
						// set to return array
						$return['tagline'] = $reader->readString();
					case 'actor':
						// get name and thumb
						$Foo = new SimpleXMLElement($reader->readOuterXml());
						$actor_list .= $Foo->name . ', ';
						$actor_thumb_list .= $Foo->thumb . ', ';
						break;
					case 'mpaa':
						// set to return array
						$return['mpaa'] = $reader->readString();
						break;
					
				}
			}
		}

		// remove last comma
		$actor_list = rtrim($actor_list, ', ');
		$actor_thumb_list = rtrim($actor_thumb_list, ', ');
		$genre_list = rtrim($genre_list, ', ');
		
		// create actor name and thumb url array
		$actor_name_ar = explode(', ',$actor_list);
		$actor_thumb_ar = explode(', ',$actor_thumb_list);
		
		// cleanup actor name list
		array_map("addslashes",$actor_name_ar);
		array_map("htmlspecialchars",$actor_name_ar);

		// loop thru comma delimeted actor list
		for ($i=0; $i< count($actor_name_ar); $i++) {
		
			// check if this index exists
			if (array_key_exists($i,$actor_name_ar) && array_key_exists($i,$actor_thumb_ar)) {
				
				// check if this actor is empty
				if (!empty($actor_name_ar[$i])) {
					
					// replace double quotes with single quotes
					$actor_name_ar[$i] = str_replace('"',"'",$actor_name_ar[$i]);

				}
				
			}
		}

		// add actors to return
		$return['actors'] = $actor_list;

		// add genres raw to return
		$return['genres_raw'] = $genre_list;

		// now return this data
		return $return;

	}

}
?>