<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class LandingController extends Controller
{
    public function search(Request $request)
    {
        // $category = Input::get('category', 'default category');
        $query = $request->input('q');
        $rank = $request->input('rank');

        $process = new Process("python3 query.py indexdb {$rank} \"{$query}\"");
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $list_data = array_filter(explode("\n",$process->getOutput()));
        
        $data = array();

        foreach ($list_data as $book) {
            $dataj =  json_decode($book, true);
            array_push($data, '
            <div class="col-lg-5">
                <div class="card mb-2">
                    <div style="display: flex; flex: 1 1 auto;">
                        <div class="img-square-wrapper">
                            <img src="http://books.toscrape.com/'.$dataj['image'].'">
                        </div>
                        <div class="card-body">
                            <h6 class="card-title"><a target="_blank" href="http://books.toscrape.com/catalogue/'.$dataj['url'].'">'.$dataj['title'].'</a></h6>
                            <p class="card-text text-success">Price : '.$dataj['price'].'</p>
                        </div>
                    </div>
                
                </div>
            </div>
            ');
        }

        echo json_encode($data);

        
    }
}
