<?php

namespace spec\Jehaby\Viomedia;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DataManagerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Jehaby\\Viomedia\\DataManager');
    }


    public function adds_folder_to_root()
    {
        $this->addFolder('test1', '');

    }


}
