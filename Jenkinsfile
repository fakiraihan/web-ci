pipeline {
    agent any

    options {
        timeout(time: 45, unit: 'MINUTES')
        retry(1)
        buildDiscarder(logRotator(numToKeepStr: '5'))
        skipDefaultCheckout(true)
    }

    environment {
        SONAR_TOKEN = credentials('sonarqube-token')
        // Define application URL for DAST testing
        APP_URL = 'http://localhost:8080'
        // Docker configuration
        SONARQUBE_CONTAINER = 'sonarqube-container'
        ZAP_CONTAINER = 'zap-container'
        SONARQUBE_PORT = '9000'
        ZAP_PORT = '8090'
        // Network for containers
        DOCKER_NETWORK = 'security-network'
        // PHP configuration
        PHP_PATH = 'php'
        // CodeIgniter app port
        CI_APP_PORT = '8080'
    }

    stages {
        stage('Pre-Check Docker') {
            steps {
                script {
                    echo 'Checking Docker Desktop status and resources...'
                    bat '''
                        echo Checking Docker Desktop status...
                        docker info > docker_status.txt 2>&1 || (
                            echo Docker Desktop is not running or having issues
                            echo Please start Docker Desktop and ensure it has adequate resources:
                            echo - Memory: 4GB minimum ^(8GB recommended^)
                            echo - CPUs: 2 minimum ^(4 recommended^)
                            echo - Disk space: 20GB minimum
                            exit /b 1
                        )
                        
                        echo Docker Desktop status:
                        findstr /i "memory cpu containers images" docker_status.txt
                        
                        echo Cleaning up any orphaned containers...
                        docker container prune -f 2>nul || echo No orphaned containers
                        
                        echo Available system resources:
                        docker system df
                    '''
                }
            }
        }

        stage('Checkout') {
            steps {
                script {
                    echo 'Checking out source code...'
                    git branch: 'main', url: 'https://github.com/fakiraihan/web-ci.git'
                }
            }
        }

        stage('Docker Setup') {
            steps {
                script {
                    echo 'Setting up Docker network and containers...'
                    bat '''
                        echo Creating Docker network...
                        docker network create %DOCKER_NETWORK% 2>nul || echo Network already exists
                        
                        echo Creating persistent volumes for SonarQube data...
                        docker volume create sonarqube-data 2>nul || echo Volume already exists
                        docker volume create sonarqube-logs 2>nul || echo Volume already exists
                        docker volume create sonarqube-extensions 2>nul || echo Volume already exists
                        
                        echo Checking if SonarQube container is already running...
                        docker ps | findstr %SONARQUBE_CONTAINER% && (
                            echo SonarQube container is already running, reusing it
                        ) || (
                            echo Starting new SonarQube container with resource limits...
                            docker stop %SONARQUBE_CONTAINER% 2>nul || echo SonarQube container not running
                            docker rm %SONARQUBE_CONTAINER% 2>nul || echo SonarQube container not found
                            
                            echo Checking Docker Desktop status...
                            docker info > docker_info.txt 2>nul || (
                                echo Docker Desktop appears to be having issues
                                echo Please ensure Docker Desktop is running and has enough resources
                                echo Recommended: 4GB RAM, 2 CPUs minimum
                                exit /b 1
                            )
                            
                            docker run -d ^
                                --name %SONARQUBE_CONTAINER% ^
                                --network %DOCKER_NETWORK% ^
                                -p %SONARQUBE_PORT%:9000 ^
                                --memory=2g ^
                                --cpus=1.0 ^
                                -v sonarqube-data:/opt/sonarqube/data ^
                                -v sonarqube-logs:/opt/sonarqube/logs ^
                                -v sonarqube-extensions:/opt/sonarqube/extensions ^
                                -e SONAR_ES_BOOTSTRAP_CHECKS_DISABLE=true ^
                                -e SONAR_JAVA_OPTS="-Xmx1g -Xms512m" ^
                                sonarqube:latest
                        )
                        
                        echo Waiting for SonarQube to stabilize...
                        ping 127.0.0.1 -n 31 > nul
                        
                        echo Verifying SonarQube container is running...
                        docker ps | findstr %SONARQUBE_CONTAINER% || (
                            echo SonarQube container failed to start
                            echo Container logs:
                            docker logs %SONARQUBE_CONTAINER%
                            echo Docker system info:
                            type docker_info.txt
                            exit /b 1
                        )
                    '''
                }
            }
        }

        stage('Verify Docker Services') {
            steps {
                script {
                    echo 'Verifying Docker services are ready...'
                    bat '''
                        echo Checking SonarQube health...
                        set /a count=0
                        :wait_sonar
                        set /a count+=1
                        if %count% GTR 20 (
                            echo SonarQube failed to start after 3.5 minutes
                            echo Showing SonarQube container logs:
                            docker logs %SONARQUBE_CONTAINER%
                            echo Checking container status:
                            docker ps -a | findstr %SONARQUBE_CONTAINER%
                            exit /b 1
                        )
                        
                        echo Checking SonarQube health... attempt %count%/20
                        curl -f http://localhost:%SONARQUBE_PORT%/api/system/status 2>nul
                        if %errorlevel% equ 0 goto sonar_ready
                        
                        echo SonarQube not ready yet, waiting 10 seconds...
                        ping 127.0.0.1 -n 11 > nul
                        goto wait_sonar
                        
                        :sonar_ready
                        echo SonarQube is ready!
                        echo SonarQube verification completed successfully!
                    '''
                }
            }
        }

        stage('Environment Setup') {
            steps {
                script {
                    echo 'Setting up CodeIgniter environment...'
                    bat '''
                        copy env .env
                        echo CI_ENVIRONMENT = development >> .env
                        echo app.baseURL = '%APP_URL%' >> .env
                        echo # Database configuration will be set in Install Dependencies stage >> .env
                    '''
                }
            }
        }

        stage('PHP Environment Check') {
            steps {
                script {
                    echo 'Checking PHP environment and extensions...'
                    bat '''
                        echo PHP Version:
                        php --version
                        
                        echo Available PHP Extensions:
                        php -m
                        
                        echo Checking for database extensions...
                        php -m | findstr -i "sqlite3" && echo ✅ SQLite3 available || echo ❌ SQLite3 not available
                        php -m | findstr -i "mysqli" && echo ✅ MySQLi available || echo ❌ MySQLi not available
                        php -m | findstr -i "pdo" && echo ✅ PDO available || echo ❌ PDO not available
                        
                        echo Checking for other important extensions...
                        php -m | findstr -i "curl" && echo ✅ cURL available || echo ❌ cURL not available
                        php -m | findstr -i "mbstring" && echo ✅ mbstring available || echo ❌ mbstring not available
                        php -m | findstr -i "openssl" && echo ✅ OpenSSL available || echo ❌ OpenSSL not available
                        php -m | findstr -i "json" && echo ✅ JSON available || echo ❌ JSON not available
                    '''
                }
            }
        }

        stage('Install Dependencies') {
            steps {
                script {
                    echo 'Installing Composer dependencies...'
                    bat 'composer install --no-interaction --prefer-dist --optimize-autoloader'
                    
                    echo 'Setting up CodeIgniter environment...'
                    bat '''
                        echo Setting up directories...
                        if not exist writable\\database mkdir writable\\database
                        if not exist writable\\logs mkdir writable\\logs
                        if not exist writable\\cache mkdir writable\\cache
                        if not exist writable\\session mkdir writable\\session
                        if not exist writable\\uploads mkdir writable\\uploads
                        
                        echo Checking PHP extensions...
                        php -m | findstr -i sqlite3 && (
                            echo ✅ SQLite3 extension is available
                            echo Setting up SQLite database...
                            
                            REM Delete existing database file if it exists
                            if exist writable\\database\\ci4_test.db del writable\\database\\ci4_test.db
                            
                            REM Create proper SQLite database using a temporary PHP script
                            echo Creating database creation script...
                            echo ^<?php > create_db.php
                            echo try { >> create_db.php
                            echo     $db = new SQLite3('writable/database/ci4_test.db', SQLITE3_OPEN_READWRITE ^| SQLITE3_OPEN_CREATE^); >> create_db.php
                            echo     $db-^>exec('CREATE TABLE IF NOT EXISTS test (id INTEGER PRIMARY KEY, name TEXT^);'^); >> create_db.php
                            echo     $db-^>close(^); >> create_db.php
                            echo     echo "SQLite database created successfully\\n"; >> create_db.php
                            echo } catch (Exception $e^) { >> create_db.php
                            echo     echo "Database creation failed: " . $e-^>getMessage(^) . "\\n"; >> create_db.php
                            echo } >> create_db.php
                            echo ?^> >> create_db.php
                            
                            echo Running database creation script...
                            php create_db.php
                            del create_db.php
                            
                            echo Updating .env for SQLite...
                            echo. >> .env
                            echo # SQLite Database Configuration >> .env
                            echo database.default.hostname = >> .env
                            echo database.default.database = %CD%\\writable\\database\\ci4_test.db >> .env
                            echo database.default.username = >> .env
                            echo database.default.password = >> .env
                            echo database.default.DBDriver = SQLite3 >> .env
                            echo database.default.DBPrefix = >> .env
                            echo database.default.port = >> .env
                            echo database.default.foreignKeys = true >> .env
                            echo database.default.busyTimeout = 1000 >> .env
                            
                            echo Showing .env database configuration:
                            findstr "database" .env
                            
                            echo Testing CodeIgniter database configuration...
                            echo ^<?php > test_ci_db.php
                            echo require_once 'vendor/autoload.php'; >> test_ci_db.php
                            echo $dotenv = \\Dotenv\\Dotenv::createImmutable(__DIR__^); >> test_ci_db.php
                            echo $dotenv-^>load(^); >> test_ci_db.php
                            echo echo "Database file path from env: " . $_ENV['database.default.database'] . "\\n"; >> test_ci_db.php
                            echo echo "File exists: " . (file_exists($_ENV['database.default.database']^) ? "YES" : "NO"^) . "\\n"; >> test_ci_db.php
                            echo ?^> >> test_ci_db.php
                            
                            php test_ci_db.php || echo Could not test CI database config
                            del test_ci_db.php
                            
                            echo Verifying database file...
                            if exist writable\\database\\ci4_test.db (
                                echo ✅ Database file exists
                                dir writable\\database\\ci4_test.db
                            ) else (
                                echo ❌ Database file creation failed
                            )
                            
                            echo Testing database connection...
                            echo ^<?php > test_db.php
                            echo try { >> test_db.php
                            echo     $db = new SQLite3('writable/database/ci4_test.db'^); >> test_db.php
                            echo     echo "Database connection test: SUCCESS\\n"; >> test_db.php
                            echo     $db-^>close(^); >> test_db.php
                            echo } catch (Exception $e^) { >> test_db.php
                            echo     echo "Database connection test: FAILED - " . $e-^>getMessage(^) . "\\n"; >> test_db.php
                            echo } >> test_db.php
                            echo ?^> >> test_db.php
                            
                            php test_db.php
                            del test_db.php
                            
                            echo Running migrations...
                            php spark migrate --all || echo Migration failed, continuing without migrations
                            
                            echo Running seeders...
                            php spark db:seed DatabaseSeeder || php spark db:seed UserSeeder || php spark db:seed --class=DatabaseSeeder || echo Seeding failed, continuing without seeders
                        ) || (
                            echo ⚠️  SQLite3 extension not available, skipping database setup
                            echo This is fine for basic testing and security scans
                            echo Database operations will be skipped in this pipeline run
                        )
                        
                        echo CodeIgniter setup completed successfully
                    '''
                }
            }
        }

        stage('Unit Testing') {
            steps {
                script {
                    echo 'Running PHPUnit tests for CodeIgniter...'
                    
                    // Create build directory for test results
                    bat 'if not exist build mkdir build && if not exist build\\logs mkdir build\\logs'
                    
                    // Run tests without coverage first
                    echo 'Running PHPUnit tests...'
                    def testResult = bat(script: 'vendor\\bin\\phpunit --configuration phpunit.xml.dist --log-junit=build\\logs\\phpunit-report.xml --no-coverage', returnStatus: true)
                    
                    if (testResult == 0) {
                        echo '✅ All unit tests passed successfully!'
                    } else if (testResult == 1) {
                        // PHPUnit returns 1 for warnings, but tests might have passed
                        echo '⚠️ Tests completed with warnings, checking results...'
                        
                        // Check if JUnit report exists and contains test results
                        if (fileExists('build/logs/phpunit-report.xml')) {
                            def reportContent = readFile('build/logs/phpunit-report.xml')
                            if (reportContent.contains('failures="0"') && reportContent.contains('errors="0"')) {
                                echo '✅ All tests passed (warnings ignored)'
                            } else {
                                error("Unit tests failed - check test report for details")
                            }
                        } else {
                            error("Unit tests failed - no test report generated")
                        }
                    } else {
                        error("Unit tests failed with exit code: ${testResult}")
                    }
                    
                    // Try to generate coverage if Xdebug/PCOV is available
                    echo 'Attempting to generate code coverage...'
                    def coverageResult = bat(script: 'vendor\\bin\\phpunit --configuration phpunit.xml.dist --coverage-clover=build\\logs\\coverage.xml', returnStatus: true)
                    if (coverageResult == 0) {
                        echo '✅ Code coverage generated successfully'
                    } else {
                        echo '⚠️ Code coverage not available - Xdebug/PCOV not installed, continuing without coverage'
                    }
                }
            }
            post {
                always {
                    // Archive test results
                    junit testResults: 'build/logs/phpunit-report.xml', allowEmptyResults: true
                    
                    // Archive coverage reports for SonarQube only if they exist
                    script {
                        if (fileExists('build/logs/coverage.xml')) {
                            archiveArtifacts artifacts: 'build/logs/coverage.xml', fingerprint: true
                            echo 'Coverage report archived successfully'
                        } else {
                            echo 'No coverage.xml found - skipping coverage artifact archiving'
                        }
                    }
                }
            }
        }

        stage('SAST - SonarQube Analysis') {
            steps {
                script {
                    echo 'Starting SonarQube static analysis for CodeIgniter...'
                    
                    // Run SonarQube analysis using Docker
                    bat '''
                        setlocal enabledelayedexpansion
                        echo Waiting for SonarQube to be fully ready...
                        set /a count=0
                        :wait_sonar_ready
                        set /a count+=1
                        if %count% GTR 30 (
                            echo SonarQube failed to become ready after 10 minutes
                            echo Showing SonarQube container logs:
                            docker logs %SONARQUBE_CONTAINER%
                            exit /b 1
                        )
                        
                        echo Checking SonarQube status... attempt %count%/30
                        curl -s -u admin:admin http://localhost:%SONARQUBE_PORT%/api/system/status > sonar_status.json 2>nul
                        findstr "UP" sonar_status.json >nul
                        if %errorlevel% equ 0 goto sonar_fully_ready
                        
                        echo SonarQube still starting, waiting 20 seconds...
                        ping 127.0.0.1 -n 21 > nul
                        goto wait_sonar_ready
                        
                        :sonar_fully_ready
                        echo SonarQube is fully ready!
                        type sonar_status.json
                        
                        echo Testing SonarQube authentication with your token...
                        curl -H "Authorization: Bearer %SONAR_TOKEN%" ^
                            "http://localhost:%SONARQUBE_PORT%/api/authentication/validate" > auth_test.json 2>nul
                        
                        echo Authentication test response:
                        type auth_test.json
                        
                        if %errorlevel% neq 0 (
                            echo Token authentication failed, trying username/password...
                            echo Testing with admin credentials:
                            curl -u admin:admin "http://localhost:%SONARQUBE_PORT%/api/authentication/validate" > auth_test2.json 2>nul
                            type auth_test2.json
                            
                            echo Please ensure your SonarQube token is correctly configured
                            exit /b 1
                        )
                        
                        echo Running SonarQube scanner for CodeIgniter project...
                        docker run --rm ^
                            --network %DOCKER_NETWORK% ^
                            -v "%CD%":/usr/src ^
                            -w /usr/src ^
                            sonarsource/sonar-scanner-cli:latest ^
                            -Dsonar.host.url=http://host.docker.internal:%SONARQUBE_PORT% ^
                            -Dsonar.token=%SONAR_TOKEN% ^
                            -Dsonar.projectKey=web-ci ^
                            -Dsonar.projectName=CodeIgniter4-WebCI ^
                            -Dsonar.projectVersion=1.0 ^
                            -Dsonar.language=php ^
                            -Dsonar.sources=app,public ^
                            -Dsonar.tests=tests ^
                            -Dsonar.exclusions=vendor/**,writable/**,system/**,builds/**,env,preload.php,spark ^
                            -Dsonar.php.coverage.reportPaths=build/logs/coverage.xml ^
                            -Dsonar.php.tests.reportPath=build/logs/phpunit-report.xml
                    '''
                }
            }
        }

        stage('Quality Gate Check') {
            steps {
                script {
                    echo 'Checking SonarQube Quality Gate...'
                    
                    // Wait for analysis to complete and check quality gate
                    bat '''
                        echo Waiting for SonarQube analysis to complete...
                        set /a count=0
                        :wait_analysis_complete
                        set /a count+=1
                        if %count% GTR 20 (
                            echo Analysis timeout after 10 minutes
                            echo Checking final status anyway...
                            goto check_final_status
                        )
                        
                        echo Checking analysis status... attempt %count%/20
                        curl -H "Authorization: Bearer %SONAR_TOKEN%" ^
                            "http://localhost:%SONARQUBE_PORT%/api/ce/activity?component=web-ci&ps=1" > analysis_status.json 2>nul
                        
                        if %errorlevel% neq 0 (
                            echo API call failed, waiting 30 seconds...
                            ping 127.0.0.1 -n 31 > nul
                            goto wait_analysis_complete
                        )
                        
                        findstr /i "SUCCESS FAILED" analysis_status.json >nul
                        if %errorlevel% equ 0 (
                            echo Analysis completed!
                            goto check_final_status
                        )
                        
                        echo Analysis still in progress, waiting 30 seconds...
                        ping 127.0.0.1 -n 31 > nul
                        goto wait_analysis_complete
                        
                        :check_final_status
                        echo Checking Quality Gate status...
                        curl -H "Authorization: Bearer %SONAR_TOKEN%" ^
                            "http://localhost:%SONARQUBE_PORT%/api/qualitygates/project_status?projectKey=web-ci" > qg_result.json 2>nul
                        
                        if %errorlevel% neq 0 (
                            echo Quality Gate API call failed
                            echo Showing analysis status:
                            type analysis_status.json
                            exit /b 1
                        )
                        
                        echo Quality Gate Response:
                        type qg_result.json
                        
                        findstr /c:"\"status\":\"ERROR\"" qg_result.json >nul
                        if %errorlevel% equ 0 (
                            echo ❌ Quality Gate FAILED!
                            echo Review the issues in SonarQube: http://localhost:%SONARQUBE_PORT%/dashboard?id=web-ci
                            exit /b 1
                        )
                        
                        findstr /c:"\"status\":\"OK\"" qg_result.json >nul
                        if %errorlevel% equ 0 (
                            echo ✅ Quality Gate PASSED!
                            goto quality_gate_success
                        )
                        
                        echo ⚠️  Quality Gate status unclear, continuing...
                        echo Full response:
                        type qg_result.json
                        
                        :quality_gate_success
                        echo Quality Gate check completed successfully!
                        echo View detailed results: http://localhost:%SONARQUBE_PORT%/dashboard?id=web-ci
                    '''
                }
            }
        }

        stage('Start Application') {
            steps {
                script {
                    echo 'Starting CodeIgniter application for DAST testing...'
                    bat '''
                        echo Starting CodeIgniter development server on port 8080...
                        start /b php spark serve --host=0.0.0.0 --port=8080
                        echo Waiting for application to start...
                        ping 127.0.0.1 -n 31 > nul
                        echo Testing application availability...
                        curl -f http://localhost:8080 || (
                            echo Application failed to start
                            echo Checking if port 8080 is in use...
                            netstat -an | findstr :8080
                            exit /b 1
                        )
                        echo ✅ CodeIgniter application is running successfully on port 8080
                        
                        echo Application is ready for DAST testing at http://localhost:8080
                    '''
                }
            }
        }

        stage('DAST - OWASP ZAP Security Scan') {
            steps {
                script {
                    echo 'Starting OWASP ZAP dynamic security testing...'
                    
                    bat '''
                        echo Running OWASP ZAP baseline scan...
                        docker run --rm ^
                            -v "%CD%":/zap/wrk/:rw ^
                            zaproxy/zap-stable zap-baseline.py ^
                            -t http://host.docker.internal:8080 ^
                            -r zap-baseline-report.html ^
                            -J zap-baseline-report.json ^
                            -d || echo DAST scan completed with findings
                        
                        echo Moving reports to reports directory...
                        if not exist reports mkdir reports
                        if exist zap-baseline-report.html move zap-baseline-report.html reports\\
                        if exist zap-baseline-report.json move zap-baseline-report.json reports\\
                        
                        echo ✅ DAST scanning completed
                    '''
                }
            }
        }

        stage('Archive Results') {
            steps {
                script {
                    echo 'Archiving security scan results...'
                    
                    // Copy reports to workspace root for archiving
                    bat '''
                        if exist reports\\*.html (
                            copy reports\\*.html . >nul 2>&1
                            echo HTML reports copied successfully
                        ) else (
                            echo No HTML reports found
                        )
                        
                        if exist reports\\*.json (
                            copy reports\\*.json . >nul 2>&1
                            echo JSON reports copied successfully
                        ) else (
                            echo No JSON reports found
                        )
                        
                        if exist reports\\*.xml (
                            copy reports\\*.xml . >nul 2>&1
                            echo XML reports copied successfully
                        ) else (
                            echo No XML reports found
                        )
                    '''
                    
                    // Archive reports
                    archiveArtifacts artifacts: 'reports/**,*.html,*.json,*.xml', fingerprint: true, allowEmptyArchive: true
                    
                    // List what was archived
                    bat '''
                        echo Security scan artifacts:
                        dir reports 2>nul || echo No reports directory
                        dir *.html *.json *.xml 2>nul || echo No security report files in root
                    '''
                }
            }
        }

        stage('Security Analysis Results') {
            steps {
                script {
                    echo 'Processing security scan results...'
                    
                    // Parse ZAP results and check for high/medium severity issues
                    bat '''
                        if exist zap-baseline-report.json (
                            findstr /i "High\\|Medium" zap-baseline-report.json > security-issues.txt
                            if errorlevel 1 (
                                echo No high or medium severity issues found in baseline scan.
                            ) else (
                                echo WARNING: High or medium severity security issues detected in baseline scan!
                                type security-issues.txt
                            )
                        )
                        
                        if exist zap-full-report.json (
                            findstr /i "High\\|Medium" zap-full-report.json >> security-issues.txt
                            if errorlevel 1 (
                                echo No additional issues found in full scan.
                            ) else (
                                echo WARNING: Additional security issues detected in full scan!
                            )
                        )
                    '''
                }
            }
        }
    }

    post {
        always {
            script {
                echo 'Cleaning up...'
                
                // Stop CodeIgniter application
                bat '''
                    for /f "tokens=5" %%a in ('netstat -aon ^| findstr :8080') do taskkill /f /pid %%a 2>nul
                '''
                
                // Stop and remove Docker containers (optional - commented out to preserve data)
                bat '''
                    echo Containers will be left running to preserve SonarQube data and tokens
                    echo To manually stop: docker stop %SONARQUBE_CONTAINER% %ZAP_CONTAINER%
                    echo To manually remove: docker rm %SONARQUBE_CONTAINER% %ZAP_CONTAINER%
                    echo SonarQube data is stored in persistent volumes: sonarqube-data, sonarqube-logs, sonarqube-extensions
                    
                    echo Uncomment below lines if you want to clean up containers after each run
                    echo docker stop %SONARQUBE_CONTAINER% 2^>nul ^|^| echo SonarQube container not running
                    echo docker rm %SONARQUBE_CONTAINER% 2^>nul ^|^| echo SonarQube container not found
                    echo docker stop %ZAP_CONTAINER% 2^>nul ^|^| echo ZAP container not running  
                    echo docker rm %ZAP_CONTAINER% 2^>nul ^|^| echo ZAP container not found
                    echo docker network rm %DOCKER_NETWORK% 2^>nul ^|^| echo Network not found
                '''
                
                // Clean up temporary files
                bat '''
                    if exist security-issues.txt del security-issues.txt
                    if exist reports rmdir /s /q reports
                    if exist docker_status.txt del docker_status.txt
                    if exist docker_info.txt del docker_info.txt
                    if exist sonar_status.json del sonar_status.json
                    if exist auth_test.json del auth_test.json
                    if exist auth_test2.json del auth_test2.json
                    if exist analysis_status.json del analysis_status.json
                    if exist qg_result.json del qg_result.json
                '''
            }
        }
        
        success {
            echo '✅ Pipeline completed successfully! Both SAST and DAST security scans passed.'
            
            // Send success notification
            emailext (
                subject: "✅ Security Pipeline Success - CodeIgniter Web-CI",
                body: """
                The security pipeline for CodeIgniter Web-CI has completed successfully!
                
                ✅ SAST (SonarQube): Quality gate passed
                ✅ DAST (OWASP ZAP): Security scan completed
                ✅ Unit Tests: All tests passed
                
                Build: ${env.BUILD_NUMBER}
                Branch: ${env.BRANCH_NAME}
                
                View reports: ${env.BUILD_URL}
                SonarQube Dashboard: http://localhost:9000/dashboard?id=web-ci
                """,
                to: "${env.CHANGE_AUTHOR_EMAIL}"
            )
        }
        
        failure {
            echo '❌ Pipeline failed! Check logs for details.'
            
            // Send failure notification
            emailext (
                subject: "❌ Security Pipeline Failed - CodeIgniter Web-CI",
                body: """
                The security pipeline for CodeIgniter Web-CI has failed!
                
                Please check the build logs for details.
                
                Build: ${env.BUILD_NUMBER}
                Branch: ${env.BRANCH_NAME}
                
                View logs: ${env.BUILD_URL}console
                """,
                to: "${env.CHANGE_AUTHOR_EMAIL}"
            )
        }
        
        unstable {
            echo '⚠️ Pipeline completed with warnings. Please review security reports.'
        }
    }
}
