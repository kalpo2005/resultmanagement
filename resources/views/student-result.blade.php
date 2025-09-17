<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Result Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Core Styles & Variables */
        :root {
            --bg-dark: #111827;
            --primary-dark: #1F2937;
            --secondary-dark: #374151;
            --border-dark: #4B5563;
            --text-light: #F9FAFB;
            --text-muted: #9CA3AF;
            --accent-color: #4F46E5;
            --accent-hover: #4338CA;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-light);
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem;
        }

        main {
            width: 100%;
            max-width: 56rem;
            /* Equivalent to max-w-4xl */
            margin: auto;
        }

        /* Form Container */
        .form-container {
            background-color: var(--primary-dark);
            padding: 2rem;
            border-radius: 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .form-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .form-header h1 {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .form-header p {
            color: var(--text-muted);
            margin-top: 0.5rem;
            font-size: 0.875rem;
        }

        .form-grid {
            display: grid;
            gap: 1rem;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label,
        .radio-group-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }

        .form-input,
        .form-select {
            background-color: var(--secondary-dark);
            color: var(--text-light);
            width: 100%;
            padding: 0.6rem;
            border: 1px solid var(--border-dark);
            border-radius: 0.375rem;
            transition: all 0.3s ease;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 2px var(--accent-color);
        }

        .radio-group {
            display: flex;
            gap: 0.75rem;
            background-color: var(--secondary-dark);
            border: 1px solid var(--border-dark);
            border-radius: 0.375rem;
            padding: 0.5rem;
        }

        .radio-group label {
            flex-grow: 1;
            text-align: center;
            padding: 0.25rem 0;
            font-size: 0.875rem;
            border-radius: 0.25rem;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .radio-group input {
            display: none;
        }

        .radio-group input:checked+label {
            background-color: var(--accent-color);
            color: var(--text-light);
        }

        .btn {
            background-color: var(--accent-color);
            color: white;
            font-weight: bold;
            padding: 0.6rem 1rem;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: var(--accent-hover);
        }

        .btn:disabled {
            cursor: not-allowed;
            opacity: 0.75;
        }

        .loader {
            margin-left: 0.5rem;
        }

        .hidden {
            display: none;
        }

        /* Result Overlay & Card */
        .result-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(17, 24, 39, 0.5);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            z-index: 40;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease-in-out;
        }

        .result-section {
            width: 100%;
            max-width: 56rem;
            /* max-w-4xl */
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }

        .result-card {
            background-color: var(--primary-dark);
            color: var(--text-light);
            padding: 2rem;
            border-radius: 1.5rem;
        }

        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid var(--border-dark);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .result-header h2 {
            font-size: 1.25rem;
            font-weight: bold;
        }

        .result-header p {
            color: var(--text-muted);
        }

        .btn-print,
        .btn-close {
            background-color: var(--secondary-dark);
            color: var(--text-muted);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: background-color 0.3s ease;
        }

        .btn-print:hover,
        .btn-close:hover {
            background-color: #4B5563;
        }

        .btn-close {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            padding: 0.5rem;
            z-index: 10;
        }

        .student-info {
            display: grid;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .student-photo {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .student-photo img {
            width: 7rem;
            height: 7rem;
            border-radius: 9999px;
            object-fit: cover;
            border: 4px solid var(--border-dark);
        }

        .student-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem 1.5rem;
            font-size: 0.75rem;
        }

        .student-details div strong {
            color: var(--text-muted);
            display: block;
        }

        .student-details div span {
            font-weight: 600;
        }

        /* Marks Table */
        .table-container {
            overflow-x: auto;
        }

        .marks-table {
            width: 100%;
            text-align: left;
            font-size: 0.875rem;
            border-collapse: collapse;
        }

        .marks-table thead {
            background-color: var(--bg-dark);
        }

        .marks-table th,
        .marks-table td {
            padding: 0.75rem;
            border: 1px solid var(--border-dark);
        }

        .marks-table th {
            font-weight: 600;
            color: var(--text-muted);
        }

        .marks-table tbody tr {
            border-bottom: 1px solid var(--border-dark);
        }

        .marks-table tfoot {
            background-color: var(--bg-dark);
            font-weight: bold;
        }

        .marks-table .text-center {
            text-align: center;
        }

        .marks-table .text-right {
            text-align: right;
        }

        /* Result Summary */
        .result-summary {
            display: grid;
            gap: 1rem;
            margin-top: 1.5rem;
            text-align: center;
        }

        .summary-box {
            padding: 0.75rem;
            border-radius: 0.5rem;
        }

        .summary-box p:first-child {
            font-size: 0.75rem;
            font-weight: 600;
        }

        .summary-box p:last-child {
            font-size: 1.25rem;
            font-weight: bold;
        }

        .summary-sgpa {
            background-color: rgba(79, 70, 229, 0.2);
        }

        .summary-sgpa p:first-child {
            color: #A5B4FC;
        }

        .summary-result.pass {
            background-color: rgba(22, 163, 74, 0.2);
        }

        .summary-result.pass p:first-child {
            color: #86EFAC;
        }

        .summary-result.fail {
            background-color: rgba(220, 38, 38, 0.2);
        }

        .summary-result.fail p:first-child {
            color: #FCA5A5;
        }

        .summary-cgpa {
            background-color: var(--secondary-dark);
        }

        .summary-cgpa p:first-child {
            color: var(--text-muted);
        }

        /* Dialog Box */
        .dialog-box {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            z-index: 50;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease-in-out;
        }

        .dialog-content {
            background-color: var(--primary-dark);
            border-radius: 0.75rem;
            padding: 1.5rem;
            width: 100%;
            max-width: 24rem;
            text-align: center;
            transform: scale(0.95);
            transition: transform 0.3s ease-in-out;
        }

        .dialog-icon {
            width: 4rem;
            height: 4rem;
            border-radius: 9999px;
            margin: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.25rem;
            margin-bottom: 1rem;
        }

        .dialog-icon.success {
            background-color: rgba(22, 163, 74, 0.2);
            color: #4ADE80;
        }

        .dialog-icon.error {
            background-color: rgba(220, 38, 38, 0.2);
            color: #F87171;
        }

        .dialog-content h3 {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .dialog-content p {
            color: var(--text-muted);
        }

        .dialog-close-btn {
            margin-top: 1.5rem;
            width: 100%;
            background-color: var(--secondary-dark);
        }

        .dialog-close-btn:hover {
            background-color: var(--border-dark);
        }

        /* Responsive Styles */
        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .student-info {
                grid-template-columns: 1fr 2fr;
            }

            .result-summary {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .form-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Print Styles */
        @media print {
            body {
                background-color: white !important;
            }

            body * {
                visibility: hidden;
            }

            .result-overlay {
                position: static;
                background: none;
                backdrop-filter: none;
                padding: 0;
            }

            .printable-area,
            .printable-area * {
                visibility: visible;
                color: black !important;
            }

            .printable-area {
                position: static;
                width: 100%;
                max-height: none;
                overflow: visible;
                background-color: white !important;
                box-shadow: none;
            }

            .result-card {
                background-color: white !important;
                border: 1px solid #ccc;
                border-radius: 0;
                padding: 1.5rem;
            }

            .marks-table,
            .marks-table th,
            .marks-table td {
                border-color: #ddd !important;
            }

            .marks-table thead,
            .marks-table tfoot {
                background-color: #f9f9f9 !important;
            }

            .summary-box {
                background-color: #f0f0f0 !important;
                border: 1px solid #ddd;
            }

            .btn-print,
            .btn-close {
                display: none;
            }
        }
    </style>
</head>

<body>

    <main>
        <div class="form-container">
            <div class="form-header">
                <h1>Student Result Portal</h1>
                <p>Enter your details to view your exam result.</p>
            </div>

            <form id="result-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="examType">Exam Type</label>
                        <select id="examType" name="examTypeId" class="form-select">
                            <option value="">Select Exam</option>
                            <option value="1">APRIL-MAY-2025</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="seatNumber">Seat Number</label>
                        <input type="text" id="seatNumber" name="seatNumber" class="form-input" placeholder="e.g., 26840604">
                    </div>
                    <div class="form-group">
                        <label for="semester">Semester (Optional)</label>
                        <select id="semester" name="semesterId" class="form-select">
                            <option value="">Any</option>
                            <option value="1">Semester 1</option>
                            <option value="2">Semester 2</option>
                            <option value="3">Semester 3</option>
                            <option value="4">Semester 4</option>
                            <option value="5">Semester 5</option>
                            <option value="6">Semester 6</option>
                            <option value="7">Semester 7</option>
                            <option value="8">Semester 8</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" id="submitBtn" class="btn">
                            <span id="btn-text">Get Result</span>
                            <i id="loader" class="fas fa-spinner fa-spin loader hidden"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="radio-group-label">Class (Optional)</label>
                    <div class="radio-group">
                        <input type="radio" id="classA" name="studentClass" value="A"><label for="classA">A</label>
                        <input type="radio" id="classB" name="studentClass" value="B"><label for="classB">B</label>
                        <input type="radio" id="classC" name="studentClass" value="C"><label for="classC">C</label>
                        <input type="radio" id="classD" name="studentClass" value="D"><label for="classD">D</label>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <div id="result-overlay" class="result-overlay">
        <div id="result-section" class="result-section printable-area">
            <div class="result-card">
                <button id="closeResultBtn" class="btn-close">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width:1.5rem; height:1.5rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <div class="result-header">
                    <div>
                        <h2>Statement of Marks</h2>
                        <p id="examName"></p>
                    </div>
                    <button id="printBtn" class="btn-print">
                        <i class="fas fa-print" style="margin-right: 0.5rem;"></i> Print
                    </button>
                </div>
                <div class="student-info">
                    <div class="student-photo">
                        <img id="profileImage" src="https://placehold.co/150x150/374151/9CA3AF?text=?" alt="Student Profile Image">
                    </div>
                    <div class="student-details">
                        <div><strong>Student Name:</strong> <span id="studentName"></span></div>
                        <div><strong>Seat No:</strong> <span id="seatNo"></span></div>
                        <div><strong>Semester:</strong> <span id="semesterName"></span></div>
                    </div>
                </div>
                <div class="table-container">
                    <table class="marks-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Subject Name</th>
                                <th class="text-center">Credit</th>
                                <th class="text-center">Grade</th>
                                <th class="text-center">CCE</th>
                                <th class="text-center">SEE</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody id="marks-body"></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-right">Total</td>
                                <td id="totalCredit" class="text-center"></td>
                                <td></td>
                                <td id="totalCCE" class="text-center"></td>
                                <td id="totalSEE" class="text-center"></td>
                                <td id="grandTotal" class="text-center"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="result-summary">
                    <div class="summary-box summary-sgpa">
                        <p>SGPA</p>
                        <p id="sgpa"></p>
                    </div>
                    <div id="resultStatusContainer" class="summary-box summary-result">
                        <p>Result</p>
                        <p id="resultStatus"></p>
                    </div>
                    <div class="summary-box summary-cgpa">
                        <p>CGPA</p>
                        <p id="cgpa"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="dialog-box" class="dialog-box">
        <div id="dialog-content" class="dialog-content">
            <div id="dialog-icon" class="dialog-icon"></div>
            <h3 id="dialog-title"></h3>
            <p id="dialog-message"></p>
            <button id="dialog-close" class="btn dialog-close-btn">Close</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('result-form');
            const submitBtn = document.getElementById('submitBtn');
            const loader = document.getElementById('loader');
            const btnText = document.getElementById('btn-text');
            const resultOverlay = document.getElementById('result-overlay');
            const closeResultBtn = document.getElementById('closeResultBtn');
            const printBtn = document.getElementById('printBtn');

            // Dialog elements
            const dialogBox = document.getElementById('dialog-box');
            const dialogContent = document.getElementById('dialog-content');
            const dialogIcon = document.getElementById('dialog-icon');
            const dialogTitle = document.getElementById('dialog-title');
            const dialogMessage = document.getElementById('dialog-message');
            const dialogClose = document.getElementById('dialog-close');

            // API Configuration
            const API_BASE_URL = 'http://127.0.0.1:8000/api';
            const jwtToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL2xvZ2luYWRtaW4iLCJpYXQiOjE3NTgxMjIxMDgsImV4cCI6MTc1ODEyNTcwOCwibmJmIjoxNzU4MTIyMTA4LCJqdGkiOiJQZ3N3akp6MVdyWXY2ekN1Iiwic3ViIjoiMSIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.bYfYNJgRUGYK8zAZ-YL3N6CYw89wY3TSBXKnzfY4s9U";

            const showLoader = (show) => {
                loader.classList.toggle('hidden', !show);
                btnText.textContent = show ? 'Fetching...' : 'Get Result';
                submitBtn.disabled = show;
            };

            const showDialog = (title, message, type = 'error') => {
                dialogTitle.textContent = title;
                dialogMessage.textContent = message;
                dialogIcon.className = `dialog-icon ${type}`;
                dialogIcon.innerHTML = type === 'success' ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>';
                dialogBox.style.opacity = '1';
                dialogBox.style.pointerEvents = 'auto';
                dialogContent.style.transform = 'scale(1)';
            };

            const hideDialog = () => {
                dialogBox.style.opacity = '0';
                dialogBox.style.pointerEvents = 'none';
                dialogContent.style.transform = 'scale(0.95)';
            };

            const showResultOverlay = () => {
                resultOverlay.style.opacity = '1';
                resultOverlay.style.pointerEvents = 'auto';
            };

            const hideResultOverlay = () => {
                resultOverlay.style.opacity = '0';
                resultOverlay.style.pointerEvents = 'none';
            };

            dialogClose.addEventListener('click', hideDialog);
            closeResultBtn.addEventListener('click', hideResultOverlay);
            resultOverlay.addEventListener('click', (e) => {
                if (e.target === resultOverlay) {
                    hideResultOverlay();
                }
            });

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                hideResultOverlay();

                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                if (!data.examTypeId || !data.seatNumber) {
                    showDialog('Input Error', 'Please select an exam type and enter your seat number.');
                    return;
                }

                showLoader(true);

                // Construct the payload dynamically
                const payload = {
                    action: "getall",
                    examTypeId: data.examTypeId,
                    seatNumber: data.seatNumber
                };
                if (data.semesterId) {
                    payload.semesterId = data.semesterId;
                }
                if (data.studentClass) {
                    payload.studentClass = data.studentClass;
                }

                try {
                    const response1 = await fetch(`${API_BASE_URL}/result`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${jwtToken}`
                        },
                        body: JSON.stringify(payload)
                    });

                    if (!response1.ok) throw new Error(`HTTP error! Status: ${response1.status}`);
                    const result1 = await response1.json();
                    if (!result1.status || result1.data.length === 0) {
                        throw new Error(result1.message || 'No result found for the provided details.');
                    }

                    const resultId = result1.data[0].resultId;
                    const generalResultData = result1.data[0];

                    const response2 = await fetch(`${API_BASE_URL}/result/subject`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${jwtToken}`
                        },
                        body: JSON.stringify({
                            action: 'getall',
                            resultId: resultId
                        })
                    });

                    if (!response2.ok) throw new Error(`HTTP error! Status: ${response2.status}`);
                    const result2 = await response2.json();
                    if (!result2.status || result2.data.length === 0) {
                        throw new Error(result2.message || 'Could not fetch subject details.');
                    }

                    populateResultData(generalResultData, result2.data[0]);

                } catch (error) {
                    console.error('API Error:', error);
                    showDialog('Request Failed', error.message || 'An unknown error occurred.');
                } finally {
                    showLoader(false);
                }
            });

            const populateResultData = (generalData, subjectData) => {
                const student = subjectData.student;
                document.getElementById('studentName').textContent = student.fullName || 'N/A';
                document.getElementById('seatNo').textContent = generalData.seatNumber || 'N/A';
                document.getElementById('semesterName').textContent = subjectData.semester.semesterName || 'N/A';
                document.getElementById('examName').textContent = subjectData.examType.examName || 'N/A';

                const profileImg = document.getElementById('profileImage');
                if (student.profileImage) {
                    profileImg.src = student.profileImage;
                } else {
                    profileImg.src = `https://placehold.co/150x150/374151/9CA3AF?text=${student.firstName.charAt(0)}`;
                }

                const marksBody = document.getElementById('marks-body');
                marksBody.innerHTML = '';
                let totalCredits = 0,
                    totalCCEObt = 0,
                    totalSEEObt = 0,
                    grandTotalObt = 0;

                subjectData.subjects.forEach(subject => {
                    totalCredits += Number(subject.credit) || 0;
                    totalCCEObt += Number(subject.cce_obtained) || 0;
                    totalSEEObt += Number(subject.see_obtained) || 0;
                    grandTotalObt += Number(subject.total_obtained) || 0;
                    marksBody.innerHTML += `
                <tr>
                    <td>${subject.subject_code}</td><td>${subject.subject_name}</td><td class="text-center">${subject.credit}</td>
                    <td class="text-center">${subject.letter_grade}</td><td class="text-center">${subject.cce_obtained}/${subject.cce_max_min.split('/')[0]}</td>
                    <td class="text-center">${subject.see_obtained}/${subject.see_max_min.split('/')[0]}</td><td class="text-center">${subject.total_obtained}/${subject.total_max_min.split('/')[0]}</td>
                </tr>`;
                });

                document.getElementById('totalCredit').textContent = totalCredits;
                document.getElementById('totalCCE').textContent = `${totalCCEObt}/${generalData.total_cce_max_min.split('/')[0]}`;
                document.getElementById('totalSEE').textContent = `${totalSEEObt}/${generalData.total_see_max_min.split('/')[0]}`;
                document.getElementById('grandTotal').textContent = `${grandTotalObt}/${generalData.total_marks_max_min.split('/')[0]}`;

                document.getElementById('sgpa').textContent = generalData.sgpa || 'N/A';
                document.getElementById('cgpa').textContent = generalData.cgpa || 'N/A';

                const resultStatusEl = document.getElementById('resultStatus');
                const resultContainer = document.getElementById('resultStatusContainer');
                resultStatusEl.textContent = generalData.result || 'N/A';
                resultContainer.className = 'summary-box summary-result'; // Reset
                resultContainer.classList.add(generalData.result?.toLowerCase() === 'pass' ? 'pass' : 'fail');

                showResultOverlay();
            };

            printBtn.addEventListener('click', () => window.print());
        });
    </script>
</body>

</html>