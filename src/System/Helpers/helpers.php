<?php

if (! function_exists('nitseditor_install_extension')) {
    /**
     * Installation of extension
     * @return boolean
     */
    function nitseditor_install_extension()
    {
        return "Install plugins done";
    }
}


    /*
     *  Function type:: recursive
     * return :: number
     * Description :: generate a unique 10 digits number.
     * written by :: pankaj kumar prasad
     * */
if (! function_exists('generate_unique_id')) {

    function generate_unique_id($modelName , $colmn )
    {
        $rand_id = str_random();

        if( $modelName::where($colmn ,$rand_id )->exists() )
        {
            generate_unique_id($modelName , $colmn );
        }
        else
        {
            return $rand_id ;
        }

    }
}

    /*
     *  Function type:: recursive
     * return :: number
     * Description :: generate a unique 10 digits number.
     * written by :: pankaj kumar prasad
     * */

if(! function_exists('incremented_number')) {

    function incremented_number($modelName , $colmn , $size )
    {
        $rand_id = rand(1000,9999);

        if( $modelName::where($colmn ,$rand_id )->exists() )
        {
            generate_unique_id($modelName , $colmn );
        }
        else
        {
            return $rand_id ;
        }
    }
}

    /*
     *  Function type:: recursive
     * return :: number
     * Description :: generate a unique 10 digits number.
     * written by :: pankaj kumar prasad
     * */

if(! function_exists('generate_unique_no')) {

    function generate_unique_no($modelName , $colmn )
    {
        $rand_id = rand(100000000,999999999);

        if( $modelName::where($colmn ,$rand_id )->exists() )
        {
            generate_unique_id($modelName , $colmn );
        }
        else
        {
            return $rand_id ;
        }

    }
}

    /*
     *  Function type:: recursive
     * return :: number
     * Description :: Convert month name to number.
     * written by :: pankaj kumar prasad
     * */

if(! function_exists('convert_month_name_to_number')) {

    function convert_month_name_to_number( $value )
    {
        $month_arr= [1=>'january' ,2=>'february' ,3=>'march ' ,4=>'april' ,5=>'may' ,6=>'june' ,
            7=>'july' ,8=>'august',9=>'september' ,10=>'october' ,11=>'november' ,12=>'december'];

        return  array_search(strtolower($value), $month_arr);
    }
}

    /*
     *  Function type:: recursive
     * return :: number
     * Description :: Convert month number to name.
     * written by :: pankaj kumar prasad
     * */

if(! function_exists('convert_month_number_to_name')) {

    function convert_month_number_to_name( $value )
    {
        $month_arr= [1=>'january' ,2=>'february' ,3=>'march ' ,4=>'april' ,5=>'may' ,6=>'june' ,7=>'july' ,8=>'august'
            ,9=>'september' ,10=>'october' ,11=>'november' ,12=>'december'];

        return ucfirst($month_arr[$value]);
    }
}

    /*
     *  Function type:: recursive
     * return :: number
     * Description :: Convert base 64 to file.
     * written by :: pankaj kumar prasad
     * */

if(! function_exists('convert_base_64_to_file')) {

    function convert_base_64_to_file ( $file , $dir )
    {
        $pos = strpos($file, ';');
        $type = explode(':', substr($file, 0, $pos))[1];
        $format = explode('/', $type);

        $exploded = explode(',', $file);

        $decoded = base64_decode($exploded[1]);

        if (str_contains($exploded[0], $format[1])) {
            $extension = $format[1];
        }

        $fileName = str_random() . '.' . $extension;

        // $path = public_path().$dir.$fileName;
        //$f= file_put_contents($path, $decoded);

        $path = Storage::disk('public')->put($dir .'/'. $fileName, $decoded); // only  for decoded file.
        // $fileName =  Storage::putFile('uploaded_files/institute', $file ); // only for normal upload..
        // $path = Storage::url($dir.$fileName);

        return $fileName;

    }
}

    /*
    *  Function type:: recursive
    * return :: number
    * Description :: Delete models.
    * written by :: pankaj kumar prasad
    * */

if(! function_exists('delete_models')) {

    function delete_models(  $models ,$colmn ,$id )
    {
        try
        {
            DB::beginTransaction(  );

            foreach(  $models as $items )
            {
                $data  = $items :: where( $colmn , $id )->get();

                foreach ( $data as $rows)
                {
                    $rows->delete();

                }
            }
            DB::commit();
            return 1;
        }
        catch( \Exception $e)
        {
            DB::rollBack();
            $err =   array( "line"=> $e->getLine(), "code"=>$e->getCode() );
            // echo "Sorry Contact to Technical Team."
            return $err  ;
        }


    }

    //Env configuration involved during installations
    if ( ! function_exists('config_env_keys'))
    {
        function config_env_keys($key, $value)
        {
            $path = app()->environmentFilePath();

            $escaped = preg_quote('='.env($key), '/');

            file_put_contents($path, preg_replace(
                "/^{$key}{$escaped}/m",
                "{$key}={$value}",
                file_get_contents($path)
            ));
        }
    }
}