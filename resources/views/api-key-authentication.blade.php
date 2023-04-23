<!DOCTYPE html>
<html>
<head>
    <title>MailerLite Integration Engineer Assignment | API Key</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"/>
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.min.css'>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">API Key</h2>

        @if (count($errors) > 0)
            <div class = "alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @isset($key)
            @if($key == "invalid")
                <div class = "alert alert-danger">
                    <ul>
                        <li>Key is not valid</li>
                    </ul>
                </div>
            @endif
        @endisset

        <p class="mb-4">Enter your API Key before gaining access.</p>

        <?php echo Form::open(array('url'=>'/validation')); ?>

        <div class="form-group">
            <label for="apiKey">Key</label>
            <?php echo Form::text('apiKey', null, array('placeholder' => 'Enter your API Key', 'id' => 'apiKey', 'autofocus' => 'autofocus', 'required' => 'required')); ?>
        </div>

        <?php echo Form::submit('Contunue', array('class' => 'btn btn-primary')); ?>

        <?php echo Form::close(); ?>
    </div>
</body>
</html>
