<?php
class FileName
{
    //Формирование уникального имени файла
    public static function getRandomFileName($path, $extension=''): string
    {
        $extension = $extension ? '.' . $extension : '';
        $path = $path ? $path . '/' : '';

        do {
            $name = md5(microtime() . rand(0, 9999));
            $file = $path . $name . $extension;
        } while (file_exists($file));

        return $name;
    }


    //Сохранение аватарки
    public static function saveFile($base64, $avatarPath): string
    {

        $result = '';

        //Вытаскиваем формат картинки
        $format = explode('/',stristr($base64, ';base64', true))[1];

        //Генерируем имя
        $nameAvatar = FileName::getRandomFileName($avatarPath, '.'.$format);
        if (!empty($oldAvatar) && $oldAvatar != 'NoAvatar.jpg'){
            try{
                unlink($avatarPath.$oldAvatar);
            } catch (Exception $ex){
                chmod($avatarPath, 777);
                unlink($avatarPath.$oldAvatar);
            }
        }

        //Если base64 картники не пустой
        if (!empty($base64)){

            //Создаем файл на сервере
            FileName::base64_to_image($base64, $avatarPath.$nameAvatar.'.'.$format);
            $result = $nameAvatar.'.'.$format;
        }

        return $result;
    }

    //Сохранение base64 как картинки
    public static function base64_to_image($base64_string, $output_file) {
        // open the output file for writing
        $ifp = fopen( $output_file, 'wb' );

        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode( ',', $base64_string );

        fwrite( $ifp, base64_decode( $data[ 1 ] ) );

        // clean up the file resource
        fclose( $ifp );

    }
}