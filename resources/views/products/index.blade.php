@extends('layouts.master')
@section('title')
    المنتجات
@endsection
@section('css')
    <!-- Internal Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الإعدادات</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/
                    المنتجات</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
       <!-- validation error messages -->
       @if ($errors->any())
       <div class="alert alert-danger">
           <ul>
               @foreach ($errors->all() as $error)
                   <li>{{ $error }}</li>
               @endforeach
           </ul>
       </div>
   @endif
   <!-- End validation error messages -->
   <!-- Section Added Successfully message -->
   @if(session()->has('add'))
       <div class="alert alert-success alert-dismissible fade show" role="alert">
           <strong>{{ session()->get('add') }}</strong>
           <button type="button" class="close  fixed-top-right" data-dismiss="alert" aria-label="Close">
               <span aria-hidden="true">&times;</span>
           </button>
       </div>
   @endif
   <!-- End Section Added Successfully message-->
   <!-- Section updated Successfully message -->
    @if(session()->has('update'))
       <div class="alert alert-success alert-dismissible fade show" role="alert">
           <strong>{{ session()->get('update') }}</strong>
           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
               <span aria-hidden="true">&times;</span>
           </button>
       </div>
    @endif
   <!-- End Section Updated Successfully message-->
   <!-- Section delete Successfully message -->
   @if(session()->has('delete'))
       <div class="alert alert-success alert-dismissible fade show" role="alert">
           <strong>{{ session()->get('delete') }}</strong>
           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
               <span aria-hidden="true">&times;</span>
           </button>
       </div>
   @endif
   <!-- End Section deleted Successfully message-->
    <!-- row -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <!-- add section button -->
                <div class="row row-sm mt-3 mr-2 ">
                    <div class="col-sm-6 col-md-4 col-xl-3 ">
                        <a class="modal-effect btn btn-outline-primary btn-block text-lg" data-effect="effect-scale"
                            data-toggle="modal" href="#modaldemo8">اضافة&nbsp; منتج </a>
                    </div>
                    <!-- Endadd section button -->
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @if ($products->count() > 0)
                            <table class="table text-md-nowrap" id="example1">
                                <thead>
                                    <tr>
                                        <th class="wd-15p border-bottom-0">#</th>
                                        <th class="wd-15p border-bottom-0">اسم المنتج</th>
                                        <th class="wd-15p border-bottom-0">اسم القسم</th>
                                        <th class="wd-20p border-bottom-0"> الوصف</th>
                                        <th class="wd-15p border-bottom-0">العمليات </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $product->product_name }}</td>
                                            <td>{{ $product->section->section_name}}</td>
                                            <td>{{ $product->description }}</td>
                                            <td>

                                                <a class="modal-effect btn btn-sm btn-info" data-effect="effect-scale"
                                                    data-id="{{ $product->id }}"
                                                    data-product_name="{{ $product->product_name }}"
                                                    data-section_name="{{ $product->section->name }}"
                                                    data-section_id="{{ $product->section_id }}"
                                                    data-description="{{ $product->description }}" data-toggle="modal"
                                                    href="#edit_Product" title="تعديل"><i class="las la-pen"></i></a>

                                                <a class="modal-effect btn btn-sm btn-danger" data-effect="effect-scale"
                                                    data-id="{{ $product->id }}"
                                                    data-product_name="{{ $product->product_name }}" data-toggle="modal"
                                                    href="#delete_product" title="حذف"><i class="las la-trash"></i></a>

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="container-fluid h-100">
                                <div class="row h-100 justify-content-center align-items-center">
                                    <div class="col-md-6 text-center">
                                        <h3>لا يوجد أي منتج حاليا</h3>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Basic Modal -->
        <div class="modal" id="modaldemo8">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">اضافة منتج</h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form method="POST" action="{{ route('products.store') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="exampleInputEmail1">اسم المنتج</label>
                                <input type="text" class="form-control" id="product_name" name="product_name"
                                    required>
                            </div>
                            <label class="my-1 mr-2" for="inlineFormCustomSelectPref">القسم</label>
                                        <select name="section_id" id="section_id" class="form-control" required>
                                            <option value="" selected disabled> --حدد القسم--</option>
                                            @foreach ($sections as $section)
                                                <option value="{{ $section->id }}">{{ $section->section_name }}</option>
                                            @endforeach
                                        </select>
                            <div class="form-group">
                                <label for="exampleFormControlTextarea1">ملاحظات</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">تاكيد</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Basic Modal -->
        <!-- edit -->
        <div class="modal fade" id="edit_Product" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">تعديل المنتج</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form action="products/update" method="post" autocomplete="off">
                            {{ method_field('patch') }}
                            {{ csrf_field() }}
                            <div class="form-group">
                                <input type="hidden" name="id" id="id" value="">
                                <label for="recipient-name" class="col-form-label">اسم المنتج:</label>
                                <input class="form-control" name="product_name" id="product_name" type="text">
                            </div>
                            <label class="my-1 mr-2" for="inlineFormCustomSelectPref">القسم</label>
                            <select name="section_id" id="section_id" class="custom-select my-1 mr-sm-2" value="" required>
                                @foreach ($sections as $section)
                                    <option value="{{$section->id}}" >{{ $section->section_name }}</option>
                                @endforeach
                            </select>
                            <div class="form-group">
                                <label for="message-text" class="col-form-label">ملاحظات:</label>
                                <textarea class="form-control" id="description" name="description"></textarea>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">تاكيد</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Edit Modal -->
        <!-- delete -->
        <div class="modal" id="delete_product">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">حذف المنتج</h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="products/destroy" method="post">
                        {{ method_field('delete') }}
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <p>هل انت متاكد من عملية الحذف ؟</p><br>
                            <input type="hidden" name="id" id="id" value="">
                            <input class="form-control" name="product_name" id="product_name" type="text" readonly>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                            <button type="submit" class="btn btn-danger">تاكيد</button>
                        </div>
                </div>
                </form>
            </div>
        </div>
        <!-- End delete Modal -->
    </div>
    </div>
    <!-- row closed -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <!-- Internal Data tables -->
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/vfs_fonts.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
    <!--Internal  Datatable js -->
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
    <!-- Internal Modal js-->
    <script src="{{ URL::asset('assets/js/modal.js') }}"></script>
    <!-- Edit Modal jQuery-->
    <script>
        $('#edit_Product').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var product_name = button.data('product_name')
            var section_id = button.data('section_id')
            var description = button.data('description')
            var modal = $(this)
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #product_name').val(product_name);
            modal.find('.modal-body #section_id').val(section_id);
            modal.find('.modal-body #description').val(description);
            })
    </script>
    <!-- End Edit Modal JQuery-->
    <!-- Delete Modal jQuery-->
    <script>
        $('#delete_product').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var product_name = button.data('product_name')
            var modal = $(this)
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #product_name').val(product_name);
        })
    </script>
    <!-- End Delete Modal jQuery-->
    <!-- make any alert dispear after 9 sec  -->
    <script>
        window.setTimeout(() => {
            $('.alert').fadeTo(500,0).slideUp(function(){
                $(this).remove();
            });
        }, 9000);
    </script>
@endsection
