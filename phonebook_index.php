<?php
/*	99% gemini work
	0.5% nick's prompt
	0.5% nick's idea
	14/1/2026, completed in 5mins by AI
	13/7/2026, added Company column as "department / area"
*/
// The URL of your XML file
$url = "<YEASTAR PBX SERVER IP URL>/Company_Contacts.xml";

// 1. Read the file content as a string
$xmlString = file_get_contents($url);

// 2. Fix unescaped ampersands that are not part of an XML entity
$xmlString = preg_replace('/&(?!(?:amp|lt|gt|quot|apos|#\d+|#x[a-fA-F0-9]+);)/', '&amp;', $xmlString);

// 3. Parse the cleaned string (enable LIBXML_NOERROR to handle minor issues gracefully)
$xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOERROR | LIBXML_NOWARNING);

// Load the XML from the URL
//$xml = @simplexml_load_file($url);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Directory - YEASTAR</title>
    
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 40px; background-color: #f9f9f9; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; margin-top: 0; }
        .error { color: #d9534f; background: #f2dede; padding: 10px; border-radius: 4px; }
        /* Add some spacing to buttons */
        .dt-buttons { margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="container">
    <h2>YEASTAR Office IP Phone Directory</h2>

    <?php if ($xml === false): ?>
        <p class="error"><strong>Error:</strong> Could not retrieve or parse the XML file from <?php echo htmlspecialchars($url); ?></p>
    <?php else: ?>
        <table id="directoryTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Extension (Work)</th>
                    <th>Dept Area</th>
                </tr>
            </thead>
            <tbody><?php foreach ($xml->DirectoryEntry as $entry): ?>

                    <tr>
                        <td><?php echo htmlspecialchars($entry->Name); ?></td>
                        <td><?php 
                            $workNumber = "";
                            foreach ($entry->Telephone as $phone) {
                                if ((string)$phone['label'] === 'Work') {
                                    $workNumber = (string)$phone;
                                    break;
                                }
                            }
                            echo htmlspecialchars($workNumber);
                            ?></td>
                        <td><?php 
							$workNumber = "";
                            foreach ($entry->Telephone as $phone) {
                                if ((string)$phone['label'] === 'Company') {
                                    $DeptArea = (string)$phone;
                                    break;
                                }
                            }
                            echo htmlspecialchars($DeptArea); ?></td>
                    </tr><?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function() {
        $('#directoryTable').DataTable({
            "pageLength": 100,
            "dom": 'Bfrtip', // This tells DataTables where to place the Buttons (B)
            "buttons": [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            "order": [[ 0, "asc" ]]
        });
    });
</script>

</body>
</html>
