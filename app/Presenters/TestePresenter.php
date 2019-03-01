<?php

namespace App\Presenters;

use App\Transformers\TesteTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class TestePresenter.
 *
 * @package namespace App\Presenters;
 */
class TestePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new TesteTransformer();
    }
}
