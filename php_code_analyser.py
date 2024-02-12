import os
import re
import sys

def scan_php_file(file_path):
    with open(file_path, 'r') as file:
        php_code = file.readlines()

    # Regular expression patterns for various vulnerabilities
    sql_pattern = r'(\$pdo->\w+\(|\$mysqli->\w+\(|\$conn->\w+\(|mysqli_query\(|mysql_query\(|pg_query\()'
    xss_pattern = r'echo\s*[\(]?[\$]?_GET\s*[\[]?|echo\s*[\(]?[\$]?_POST\s*[\[]?|echo\s*[\(]?[\$]?_REQUEST\s*[\[]?|\$\w+\s*=\s*[\$]?_GET\s*[\[]?|\$\w+\s*=\s*[\$]?_POST\s*[\[]?|\$\w+\s*=\s*[\$]?_REQUEST\s*[\[]?|<\?php\s*echo\s*[\(]?[\$]?_GET\s*[\[]?|<\?php\s*echo\s*[\(]?[\$]?_POST\s*[\[]?|<\?php\s*echo\s*[\(]?[\$]?_REQUEST\s*[\[]?'
    upload_pattern = r'(move_uploaded_file\s*\( | is_uploaded_file\s*\( | \$_FILES\[ )'


    # check for password hashing
    password_hash_check = False
    password_warning = "WARNING: Potential Plain Text Password Storage.\n Recommendation: Store passwords securely using hashing algorithms (e.g., bcrypt) with salt."

    for line in php_code:
        if '$password' in line and 'password_hash()' not in line:
            password_hash_check = True

    if password_hash_check:
        print(password_warning)


    # check for html form accept type
        
    file_type_check = False
    file_type_warning = "\nWARNING: It seems that there is a form to upload a file but there is no restriction for accept type.\nRecommendation: use the 'accept' attribute. " 

    for line in php_code:
        if ('type="file"' in line or "type='file'" in line) and 'accept' not in line:
            file_type_check = True

    if file_type_check:
        print(file_type_warning)
    # Search for vulnerabilities in the PHP code
    vulnerabilities = []

    for line_num, line in enumerate(php_code, start=1):
        if re.search(sql_pattern, line):
            vulnerabilities.append((line_num, "Potential SQL injection vulnerability",
                                    "Recommendation: Use prepared statements with parameterized queries to prevent SQL injection."))
        if re.search(xss_pattern, line):
            vulnerabilities.append((line_num, "Potential Cross-Site Scripting (XSS) vulnerability",
                                    "Recommendation: Sanitize user inputs and encode output data to prevent XSS attacks."))
        if re.search(upload_pattern, line):
            vulnerabilities.append((line_num, "Potential Unrestricted File Upload vulnerability",
                                    "Recommendation: Validate file types and enforce proper file upload restrictions."))
    if vulnerabilities:
        print(f"Vulnerabilities found in file: {file_path}")
        print("Recommendation: Review the identified vulnerabilities and apply appropriate security measures.")
        print("Detected vulnerabilities:")
        for line_num, vulnerability, recommendation in vulnerabilities:
            print(f"- Line {line_num}: {vulnerability}")
            print(f"  {recommendation}")
            print(f"  {php_code[line_num - 1].strip()}")  # Print the affected line
        print("\nWARNING: These are just potential vulnerabilities based on pattern matching,and there might be some false positives and in some cases, false negatives.\nIt is necessary to double check the code before moving on to production.")
    else:
        print(f"No vulnerabilities found in file: {file_path}")

if __name__ == "__main__":
    # Check if a filename is provided as a command-line argument
    if len(sys.argv) != 2:
        print("Usage: python3 php_code_analyser.py <filename>")
        sys.exit(1)

    filename = sys.argv[1]

    # Check if the specified file exists
    if not os.path.isfile(filename):
        print("Error: File does not exist.")
        sys.exit(1)

    # Check if the specified file is a PHP file
    if not filename.endswith(".php"):
        print("Error: Not a PHP file.")
        sys.exit(1)

    scan_php_file(filename)
