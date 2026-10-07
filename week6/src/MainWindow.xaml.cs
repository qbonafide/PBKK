using System.Text.RegularExpressions;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Input;
using StudentRegistration.Models;
using StudentRegistration.Repositories;

namespace StudentRegistration;

public partial class MainWindow : Window
{
    private readonly StudentRepository _studentRepository = new();
    private readonly ProgramRepository _programRepository = new();

    // Id mahasiswa yang sedang dipilih (0 = tidak ada)
    private int _selectedId = 0;

    public MainWindow()
    {
        InitializeComponent();
        LoadPrograms();
        LoadStudents();
    }

    // =====================================================
    //  LOAD DATA
    // =====================================================
    private void LoadPrograms()
    {
        try
        {
            cmbProdi.ItemsSource = _programRepository.GetActive();
        }
        catch (Exception ex)
        {
            ShowDbError(ex);
        }
    }

    private void LoadStudents()
    {
        try
        {
            string keyword = txtSearch.Text.Trim();
            var students = _studentRepository.GetAll(keyword);
            StudentDataGrid.ItemsSource = students;

            int total = _studentRepository.GetTotal();
            lblJumlah.Text = string.IsNullOrEmpty(keyword)
                ? $"Jumlah Mahasiswa: {total}"
                : $"Jumlah Mahasiswa: {total} (ditampilkan {students.Count})";
        }
        catch (Exception ex)
        {
            ShowDbError(ex);
        }
    }

    private void RefreshButton_Click(object sender, RoutedEventArgs e)
    {
        LoadStudents();
    }

    private void TxtSearch_TextChanged(object sender, TextChangedEventArgs e)
    {
        if (!IsLoaded) return;
        LoadStudents();
    }

    // =====================================================
    //  SIMPAN (CREATE)
    // =====================================================
    private void BtnSimpan_Click(object sender, RoutedEventArgs e)
    {
        if (!ValidateForm(0)) return;

        try
        {
            _studentRepository.Insert(BuildStudent());

            MessageBox.Show("Data mahasiswa berhasil disimpan!", "Informasi",
                MessageBoxButton.OK, MessageBoxImage.Information);

            ClearForm();
            LoadStudents();
        }
        catch (Exception ex)
        {
            ShowDbError(ex);
        }
    }

    // =====================================================
    //  UPDATE (EDIT)
    // =====================================================
    private void BtnUpdate_Click(object sender, RoutedEventArgs e)
    {
        if (_selectedId == 0)
        {
            MessageBox.Show("Pilih data yang ingin diedit dari tabel!");
            return;
        }

        if (!ValidateForm(_selectedId)) return;

        try
        {
            var student = BuildStudent();
            student.StudentId = _selectedId;
            _studentRepository.Update(student);

            MessageBox.Show("Data mahasiswa berhasil diupdate!", "Informasi",
                MessageBoxButton.OK, MessageBoxImage.Information);

            ClearForm();
            LoadStudents();
        }
        catch (Exception ex)
        {
            ShowDbError(ex);
        }
    }

    // =====================================================
    //  RESET
    // =====================================================
    private void BtnReset_Click(object sender, RoutedEventArgs e)
    {
        ClearForm();
    }

    // =====================================================
    //  HAPUS (DELETE) + KONFIRMASI
    // =====================================================
    private void BtnHapus_Click(object sender, RoutedEventArgs e)
    {
        if (StudentDataGrid.SelectedItem is not Student student)
        {
            MessageBox.Show("Pilih data yang ingin dihapus!");
            return;
        }

        var konfirmasi = MessageBox.Show(
            $"Yakin ingin menghapus data berikut?\n\n{student.NIM} - {student.Name}",
            "Konfirmasi Hapus",
            MessageBoxButton.YesNo,
            MessageBoxImage.Question);

        if (konfirmasi != MessageBoxResult.Yes) return;

        try
        {
            _studentRepository.Delete(student.StudentId);
            ClearForm();
            LoadStudents();
        }
        catch (Exception ex)
        {
            ShowDbError(ex);
        }
    }

    // =====================================================
    //  PILIH BARIS DATAGRID -> ISI FORM (untuk edit)
    // =====================================================
    private void StudentDataGrid_SelectionChanged(object sender, SelectionChangedEventArgs e)
    {
        if (StudentDataGrid.SelectedItem is not Student s)
        {
            _selectedId = 0;
            btnUpdate.IsEnabled = false;
            return;
        }

        _selectedId = s.StudentId;
        btnUpdate.IsEnabled = true;

        txtNim.Text = s.NIM;
        txtNama.Text = s.Name;
        cmbProdi.SelectedValue = s.ProgramId;
        dtpLahir.SelectedDate = s.BirthDate;
        txtAlamat.Text = s.Address;
        txtTelepon.Text = s.PhoneNumber;
    }

    // =====================================================
    //  HANYA ANGKA (NIM & Telepon)
    // =====================================================
    private void Angka_PreviewTextInput(object sender, TextCompositionEventArgs e)
    {
        e.Handled = !e.Text.All(char.IsDigit);
    }

    // =====================================================
    //  VALIDASI
    // =====================================================
    private bool ValidateForm(int excludeId)
    {
        string nim = txtNim.Text.Trim();
        string nama = txtNama.Text.Trim();
        string alamat = txtAlamat.Text.Trim();
        string telp = txtTelepon.Text.Trim();

        // NIM
        if (string.IsNullOrWhiteSpace(nim))
        {
            MessageBox.Show("NIM harus diisi!");
            txtNim.Focus();
            return false;
        }
        if (!Regex.IsMatch(nim, @"^\d{8,12}$"))
        {
            MessageBox.Show("NIM harus berupa angka, 8 sampai 12 digit!");
            txtNim.Focus();
            return false;
        }

        // Nama
        if (string.IsNullOrWhiteSpace(nama))
        {
            MessageBox.Show("Nama harus diisi!");
            txtNama.Focus();
            return false;
        }
        if (nama.Length < 3 || !Regex.IsMatch(nama, @"^[\p{L} .'\-]+$"))
        {
            MessageBox.Show("Nama minimal 3 karakter dan hanya boleh berisi huruf!");
            txtNama.Focus();
            return false;
        }

        // Program studi
        if (cmbProdi.SelectedValue == null)
        {
            MessageBox.Show("Pilih program studi!");
            cmbProdi.Focus();
            return false;
        }

        // Tanggal lahir
        if (dtpLahir.SelectedDate == null)
        {
            MessageBox.Show("Tanggal lahir harus diisi!");
            dtpLahir.Focus();
            return false;
        }
        if (dtpLahir.SelectedDate.Value.Date > DateTime.Today.AddYears(-15))
        {
            MessageBox.Show("Tanggal lahir tidak valid (usia minimal 15 tahun)!");
            dtpLahir.Focus();
            return false;
        }

        // Alamat
        if (alamat.Length < 5)
        {
            MessageBox.Show("Alamat harus diisi (minimal 5 karakter)!");
            txtAlamat.Focus();
            return false;
        }

        // Telepon
        if (string.IsNullOrWhiteSpace(telp))
        {
            MessageBox.Show("Nomor telepon harus diisi!");
            txtTelepon.Focus();
            return false;
        }
        if (!Regex.IsMatch(telp, @"^(08|628)\d{8,11}$"))
        {
            MessageBox.Show("Nomor telepon harus diawali 08 atau 628, total 10-13 digit!");
            txtTelepon.Focus();
            return false;
        }

        // NIM tidak boleh duplikat
        try
        {
            if (_studentRepository.NimExists(nim, excludeId))
            {
                MessageBox.Show("NIM sudah terdaftar!");
                txtNim.Focus();
                return false;
            }
        }
        catch (Exception ex)
        {
            ShowDbError(ex);
            return false;
        }

        return true;
    }

    // =====================================================
    //  HELPER
    // =====================================================
    private Student BuildStudent()
    {
        return new Student
        {
            NIM = txtNim.Text.Trim(),
            Name = txtNama.Text.Trim(),
            ProgramId = Convert.ToInt32(cmbProdi.SelectedValue),
            BirthDate = dtpLahir.SelectedDate!.Value.Date,
            Address = txtAlamat.Text.Trim(),
            PhoneNumber = txtTelepon.Text.Trim()
        };
    }

    private void ClearForm()
    {
        txtNim.Clear();
        txtNama.Clear();
        txtAlamat.Clear();
        txtTelepon.Clear();
        cmbProdi.SelectedIndex = -1;
        dtpLahir.SelectedDate = null;
        StudentDataGrid.SelectedIndex = -1;
        _selectedId = 0;
        btnUpdate.IsEnabled = false;
        txtNim.Focus();
    }

    private static void ShowDbError(Exception ex)
    {
        MessageBox.Show(ex.Message, "Database Error",
            MessageBoxButton.OK, MessageBoxImage.Error);
    }
}