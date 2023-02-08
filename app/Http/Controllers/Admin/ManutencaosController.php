<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\Manutencao;
use App\Traits\SequencialNumberTrait;
use Illuminate\Http\Request;
use App\Traits\CrudControllerTrait;

class ManutencaosController extends Controller
{
    use CrudControllerTrait;
    use SequencialNumberTrait;

    private $model;
    private $path;
    private $redirectPath;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Manutencao $lancamentomultas)
    {
        $this->middleware('auth');
        // $this->middleware('checksetor:' . MANUTENCAODESPESAS_VISUALIZAR, ['only' => ['index']]);
        // $this->middleware('checksetor:' . MANUTENCAODESPESAS_ADICIONAR, ['only' => ['create']]);
        // $this->middleware('checksetor:' . MANUTENCAODESPESAS_EDITAR, ['only' => ['edit']]);
        // $this->middleware('checksetor:' . MANUTENCAODESPESAS_DELETAR, ['only' => ['destroy']]);
        // $this->middleware('checksetor:' . MANUTENCAODESPESAS_RELATORIO, ['only' => ['relatorio']]);


        $this->model = $lancamentomultas;
        $this->saveSetorScope = true;
        $this->path = 'admin.manutencao';
        $this->redirectPath = 'manutencao';
        $this->withFields = ['controle_frota', 'tipo_manutencao', 'fornecedor', 'tipo_correcao'];
        $this->selectModelFields = [
            'ControleFrotum' => '\App\Models\ControleFrotum',
            'TipoManutencao' => '\App\Models\TipoManutencao',
            'Fornecedor' => '\App\Models\Fornecedor',
            'TipoCorrecao' => '\App\Models\TipoCorrecao',
            'Setor' => '\App\Models\Setor',
        ];
        $this->joinSearch = [
            'controle_frota_id' => ['controle_frota', '\App\Models\ControleFrotum'],
            'tipo_manutencao_id' => ['nome', '\App\Models\TipoManutencao'],
            'fornecedor_id' => ['razao_social', '\App\Models\Fornecedor'],
            'tipo_correcao_id' => ['nome', '\App\Models\TipoCorrecao'],
            'setor_id' => ['nome', '\App\Models\Setor'],
        ];
        $this->fileName = [];
        $this->uploadFilePath = 'images/';
        $this->validations = [
            'controle_frota_id' => 'required',
            'tipo_manutencao_id' => 'required',
            'fornecedor_id' => 'required',
//            'responsavel_retirada' => 'required',
//            'descricao_manutencao' => 'required',
//            'numero_processo' => 'required',
            'data' => 'required',
            'hora' => 'required',
//            'valor' => 'required',
            'tipo_correcao_id' => 'required',
            'status' => 'required',
        ];

        $this->indexFields = [['controle_frota', 'veiculo'], ['controle_frota', 'placa'], ['data'], ['tipo_manutencao', 'nome']];
        $this->indexTitles = ['Veículo', 'Placa', 'Data', 'Manutenção/Despesas'];

        $this->pdfFields = [['data'], ['responsavel_retirada'],  ['controle_frota', 'placa'], ['setor', 'nome'], ['tipo_manutencao', 'nome'], ['descricao_manutencao'], ['fornecedor', 'razao_social'], ['valor']];
        $this->pdfTitles = ['Data', 'Responsável', 'Veículo', 'Setor', 'Tipo', 'Descrição', 'Fornecedor', 'Valor R$'];

        $this->pdfTitle = 'Manutenções/Despesas';

        $this->numbersWithDecimal = ['valor'];
    }

    public function customListagem(Request $request)
    {
        $limit = $request->all()['limit'] ?? 20;

        $result = $this->model;
        $requestData = $request->all();

        if($requestData['tipo_manutencao_id'] !== null)
            $result = $result->where('tipo_manutencao_id', '=', $requestData['tipo_manutencao_id']);

        if($requestData['controle_frota_id'] !== null)
            $result = $result->where('controle_frota_id', '=', $requestData['controle_frota_id']);

        if($requestData['data_inicial'] !== null)
            $result = $result->whereDate('data', '>=', convertTimestampToBd($requestData['data_inicial'], 'Y-m-d'));

        if($requestData['data_final'] !== null)
            $result = $result->whereDate('data', '<=', convertTimestampToBd($requestData['data_final'], 'Y-m-d'));

        if (\Gate::allows('isMasterOrAdmin')){
            if($requestData['setor_id'] !== null)
                $result = $result->where('setor_id', '=', $requestData['setor_id']);
        } else {
            $result = $result->where('setor_id', '=', auth('api')->user()->setor_id);
        }

        if ($request->export_pdf == "true")
            return $this->exportPdf($result);

        $result = $result->paginate($limit);

        return view($this->path.'.index', ['results'=>$result, 'request'=> $requestData, 'selectModelFields' => $this->selectModelFields(), 'fields' => $this->indexFields, 'titles' => $this->indexTitles]);
    }

    public function create()
    {
        return view($this->path.'.create', [
                'selectModelFields' => $this->selectModelFields(),
                'sequencial' => $this->sequencialID($this->model)
            ]
        );
    }

}
