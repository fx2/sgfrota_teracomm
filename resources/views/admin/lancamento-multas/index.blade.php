@extends('layouts.admin.index')
<style>
.square {height: 25px;width: 25px}

.gray {background-color: #AAAAAA;}
.yellow {background-color: #FFC926;}
.blue {background-color: #73B9FF;}

.blue-gray {height: 100%; background: linear-gradient(90deg, #73B9FF 50%, #AAAAAA 50%);}

.blue-yellow {height: 100%; background: linear-gradient(90deg, #73B9FF 50%, #FFC926 50%);}
</style>

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb back-transparente">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
            <li class="breadcrumb-item">Lançamento de Multas</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header h3">Lançamento de Multas</div>
        <div class="card-body">
            @can('checksetor', LANCAMENTODEMULTAS_ADICIONAR)
                <a href="{{ url('/lancamento-multas/create') }}" class="btn btn-success btn-sm" title="Add New LancamentoMulta">
                    <i class="fa fa-plus" aria-hidden="true"></i> Adicionar
                </a>
            @endcan

            <form method="GET" action="{{ url('/lancamento-multas/custom/listagem ') }}" accept-charset="UTF-8" class="form-inline my-2 my-lg-0 float-right" role="search">
                <div class="input-group">
                    <span class="input-group-append">
                        <div class="square yellow"></div> <span class="mr-3">Boleto Vencido</span>
                        <div class="square gray"></div> <span class="mr-3">Boleto Pago</span>
                        <div class="square blue"></div> <span class="mr-3">Condutor Identificado</span>
                    </span>
                    <span class="input-group-append">
                        <button type="button" class="btn btn-primary rounded" data-toggle="modal" data-target="#exampleModal">
                          Filtrar
                        </button>
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Filtrar Listagem</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                <div class="row">

                                    @include('parts/controle-frotas-index')

                                    @include('parts/motorista-index')

                                    <div class="col-12 col-sm-6	col-md-6 col-lg-6 col-xl-6 mb-2">
                                        <div class="form-group">
                                            <label for="exampleFormControlInput1">Data inícial</label>
                                            <input type="text" name="data_inicial" class="form-control data">
                                        </div>
                                    </div>

                                    <div class="col-12 col-sm-6	col-md-6 col-lg-6 col-xl-6 mb-2">
                                        <div class="form-group">
                                            <label>Data Final</label>
                                            <input type="text" name="data_final" class="form-control data">
                                        </div>
                                    </div>

                                    @include('parts/select-setor-index')

                                    @include('parts/lancamento-multas-search')
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-warning mr-4" data-dismiss="modal">Fechar</button>
                                <button type="submit" class="btn btn-primary without-pdf">Filtrar listagem</button>
                                @can('checksetor', LANCAMENTODEMULTAS_RELATORIO)
                                    <input type="hidden" class="form-control" name="export_pdf" placeholder="Buscar...">
                                    <button class="ml-3 btn btn-secondary export-pdf" type="submit">
                                        <i class="fas fa-file-pdf"></i> Filtrar PDF
                                    </button>
                                @endcan
                              </div>
                            </div>
                          </div>
                        </div>


                    </span>
                </div>
            </form>

            <br/>
            <br/>
            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-hide">
                        <tr>
{{--                            <th>#</th>--}}
                            @can('isMasterOrAdmin')
                                <th>Setor</th>
                            @endcan
                            @foreach ($titles as $item)
                                <th>{{ $item }}</th>
                            @endforeach
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $item)
                            <tr class="{{ revisaoVencimentoLancamentoMultas($item) }}">
{{--                                <td>{{ $loop->iteration }}</td>--}}
                                @can('isMasterOrAdmin')
                                    <td>{{$item->setor->nome}}</td>
                                @endcan
                                @foreach ($fields as $key => $val)
                                    <td class="borda" scope="row">
                                        @php
                                        // da pra melhorar esses if e fazer um loop, mas nao quero
                                        if (!empty($val[0])){
                                            $a = $val[0];
                                            $valor = $item->$a;

                                            if ($val[0] == 'status') {
                                                $valor = $valor == 1 ? 'Ativo' : 'Bloqueado';
                                            }
                                            elseif ($val[0] == 'data_vencimento') {
                                                $valor = convertTimestamp($valor, 'd/m/Y');
                                            }

                                        }

                                        if (!empty($val[1])){
                                            $a = $val[0];
                                            $b = $val[1];

                                            $valor = $item->$a->$b ?? '';

                                            if ($val[1] == 'status') {
                                                $valor = $valor == 1 ? 'Ativo' : 'Bloqueado';
                                            }
                                            elseif ($val[1] == 'data_vencimento') {
                                                $valor = convertTimestamp($valor, 'd/m/Y');
                                            }
                                        }

                                        if (!empty($val[2])){
                                            $a = $val[0];
                                            $b = $val[1];
                                            $c = $val[2];

                                            $valor = $item->$a->$b->$c;

                                            if ($val[2] == 'status') {
                                                $valor = $valor == 1 ? 'Ativo' : 'Bloqueado';
                                            }
                                            elseif ($val[2] == 'data_vencimento') {
                                                $valor = convertTimestamp($valor, 'd/m/Y');
                                            }
                                        }

                                        if (!empty($val[3])){
                                            $a = $val[0];
                                            $b = $val[1];
                                            $c = $val[2];
                                            $d = $val[3];

                                            $valor = $item->$a->$b->$c->$d;

                                            if ($val[3] == 'status') {
                                                $valor = $valor == 1 ? 'Ativo' : 'Bloqueado';
                                            }
                                            elseif ($val[3] == 'data_vencimento') {
                                                $valor = convertTimestamp($valor, 'd/m/Y');
                                            }
                                        }

                                        if (!empty($val[4])){
                                            $a = $val[0];
                                            $b = $val[1];
                                            $c = $val[2];
                                            $d = $val[3];
                                            $e = $val[4];

                                            $valor = $item->$a->$b->$c->$d->$e;

                                            if ($val[4] == 'status') {
                                                $valor = $valor == 1 ? 'Ativo' : 'Bloqueado';
                                            }
                                            elseif ($val[4] == 'data') {
                                                $valor = convertTimestamp($valor, 'd/m/Y');
                                            }
                                        }
                                        @endphp

                                        {{$valor}}
                                    </td>
                                @endforeach

                                <td>
                                    {{-- <a href="{{ url('/lancamento-multas/' . $item->id) }}" title="Visualizar LancamentoMulta"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></button></a> --}}
                                    @can('checksetor', LANCAMENTODEMULTAS_EDITAR)
                                        <a href="{{ url('/lancamento-multas/' . $item->id . '/edit') }}" title="Editar LancamentoMulta"><button class="btn btn-primary btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></button></a>
                                    @endcan

                                    @can('checksetor', LANCAMENTODEMULTAS_DELETAR)
                                        <button type="submit" data-id="{{ $item->id }}" data-route="/lancamento-multas" class="btnDeletar btn btn-danger btn-sm" title="Deletar LancamentoMulta"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <p class="p-1 hide-thead" onload="hideThead()">Nenhum Lançamento de Multas encontrado</p>
                        @endforelse
                    </tbody>
                </table>
                <div class="pagination-wrapper"> {!! $results->appends(['search' => Request::get('search')])->render() !!} </div>
            </div>

        </div>
    </div>
@endsection
