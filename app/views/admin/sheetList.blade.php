@extends('admin.layouts.master')
@section('scripts')
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets/admin/bower_components/bootstrap-table/bootstrap-table.css') }}" type="text/css" />
    <script src="{{ asset('assets/admin/bower_components/bootstrap-table/bootstrap-table.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/bootstrap-table/locale/bootstrap-table-pt-BR.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/boostrap-table-default-operators.js') }}"></script>

    <script type="text/javascript">
        var apiRollback = '{{ route('admin.api.sheet.rollback') }}';
        var _token = '{{ csrf_token() }}';
    </script>

    <style type="text/css">
        #table-result .fa-flag {
            color: #23527c;
        }
    </style>
@endsection
@section('content')

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">{{ $type }} - Lista</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->

            @if (Session::has('msg'))
                <div class="alert alert-{{ Session::get('msgType') == "success" ? "success" : "danger" }}">
                    {{ Session::get('msg') }}
                </div>
            @endif
            <div class="row" style="padding-bottom: 50px;">
                <div class="col-lg-12">
                    {{ Form::open(array('files' => true)) }}
                        <div class="form-group">
                            <label>Enviar novo arquivo</label>
                            {{ Form::file('sheetFile') }}
                            @if ($errors->has('sheetFile'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('sheetFile') }}</strong>
                                </span>
                            @endif
                        </div>
                        
                        @if ($type == 'Economy')
                            <div class="form-group">
                                <label>Exibir preços</label>
                                <div class="checkbox">
                                    <label>
                                        {{ Form::checkbox('showPrice', 1) }}
                                        Sim
                                    </label>
                                </div>
                                @if ($errors->has('showPrice'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('showPrice') }}</strong>
                                    </span>
                                @endif
                            </div>
                        @endif

                        <p><a href="{{ $example }}">Baixe aqui</a> o arquivo exemplo do que deve ser enviado</p>
                        <input type="submit" class="btn btn-primary" value="Enviar arquivo">
                    {{ Form::close()}}
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="dataTable_wrapper">
                                <table id="table-result" class="table table-striped table-bordered table-hover" data-toggle="table" data-side-pagination="server" data-url="{{ $api['paginate'] }}"  data-show-refresh="true"  data-page-size="20" data-search="false" data-select-item-name="toolbar1" data-pagination="true" data-sort-name="id" data-sort-order="desc" data-language="pt-BR">
                                    <thead>
                                        <tr>
                                            <th data-field="created_at" data-formatter="dateFormatter" data-sortable="true">Data de envio</th>
                                            <th data-field="original_filename" data-sortable="true">Nome do arquivo</th>
                                            <th data-field="operate" data-formatter="operateFormatter" data-events="operateEvents">Ações</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->
@endsection